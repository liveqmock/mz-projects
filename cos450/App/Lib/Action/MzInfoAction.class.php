<?php
/*
 * Created on 2014-9-17
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class MzInfoAction extends Action {

	/**
	 * 解决此错误
	 * Call to a member function display() on a non-object
	 */
	function __construct() {
		header("Content-Type:text/html; charset=utf-8");
		parent :: __construct();
	}

	/**
	 * 漫展信息
	 * */
    public function mzList(){
		header("Content-Type:text/html; charset=utf-8");
    	//展示所有漫展
		$Articlecontent = M('Articlecontent');

		$data['starttime']=time();

		//$date['cid']='6';
		//$arr = $Articlecontent->where($data)->find();

		//var_dump($data);



		//var_dump(date('y-m-d'));

		//var_dump($this->getWeekRange(date('y-m-d')));


		$dateDest = $this->getWeekRange(date('y-m-d'));//获取当前周的时间

		//var_dump($dateDest);

		//var_dump($dateDest['sdate']);
		//var_dump($dateDest['edate']);


		$data['starttime']=array('EGT',$dateDest['sdate']);//开始时间大于一周内的初始时间
		$data['closetime']=array('ELT',$dateDest['edate']);//开始时间大于一周内的初始时间

		//$arr = $Articlecontent->where($data)->select();
		//->join('RIGHT JOIN t_mz_attachment ON t_mz_attachment.aid = user_profile.typeid' );
		$sql = 'select article.cid,article.title,article.starttime,article.closetime,article.address,article.cityname,article.faceimg,attach.filename,attach.newfilename ' .
				'from t_mz_articlecontent as article, t_mz_attachment as attach where 1=1 ' .
				' and article.faceimg=attach.aid ' .
				' and article.starttime >= '.$dateDest['sdate'].'' .
						' and article.closetime <= '.$dateDest['edate'];
		$voList = $Articlecontent->query($sql);
		//var_dump($voList);

		$this->assign('data',$voList);
		//$this->assign('arr',$arr);
    	$this->display();
    }

    /**
	 * 根据漫展id查询漫展信息详情
	 */
	 function mzDetail(){

		$Articlecontent = M('Articlecontent');

		$cid = $_GET['cid'];//获取get的参数值

		$data['cid'] = $cid;//设置查询的cid

		$result = $Articlecontent->where($data)->select();

		//var_dump($result);
		$this->assign('mzArticle',$result);
		$this->display();
	 }

     // 获取指定日期所在星期的开始时间与结束时间
	 function getWeekRange($date){
	     $ret=array();
	     $timestamp=strtotime($date);
	     $w=strftime('%u',$timestamp);

	     //$ret['sdate']=date('Y-m-d 00:00:00',$timestamp-($w)*86400);
	     //$ret['edate']=date('Y-m-d 23:59:59',$timestamp+(6-$w)*86400);

	     $ret['sdate']=strtotime(date('Y-m-d 00:00:00',$timestamp-($w)*86400));
	     $ret['edate']=strtotime(date('Y-m-d 23:59:59',$timestamp+(6-$w)*86400));

	     return $ret;
	 }
	 //  以上两个函数的应用
	 function getFilter($n){
	     $ret=array();
	     switch($n){
	         case 1:// 昨天
	             $ret['sdate']=date('Y-m-d 00:00:00',strtotime('-1 day'));
	             $ret['edate']=date('Y-m-d 23:59:59',strtotime('-1 day'));
	         break;
	         case 2://本星期
	             $ret=getWeekRange(date('Y-m-d'));
	         break;
	         case 3://上一个星期
	             $strDate=date('Y-m-d',strtotime('-1 week'));
	             $ret=getWeekRange($strDate);
	         break;
	         case 4: //上上星期
	             $strDate=date('Y-m-d',strtotime('-2 week'));
	             $ret=getWeekRange($strDate);
	         break;
	         case 5: //本月
	             $ret=getMonthRange(date('Y-m-d'));
	             break;
	         case 6://上月
	             $strDate=date('Y-m-d',strtotime('-1 month'));
	             $ret=getMonthRange($strDate);
	         break;
	     }
	     return $ret;
	 }


 	/**
 	 *跳转到添加漫展页
 	 */
 	function addMz(){
 		//判断是否已经登陆
// 		echo '123123123123';
// 		var_dump($_SESSION);
 		if($_SESSION['username'] == '' || $_SESSION['userid'] == ''){
			$this->redirect('MzUser/login');
 		}else{
 			$this->display();
 		}
 	}

	/**
	 * 添加漫展信息方法
	 */
	function doAddMz(){
		//漫展发布信息表
		$Articlecontent = M('Articlecontent');

		$data["telephone"] = $_POST['contact_phone'];//电话
		$data["qqcode"] = $_POST['contact_qq'];      //qq
		$data["email"] = $_POST['contact_email'];    //邮箱

		$data["uid"] = $_POST['userId'];    //userid

		$data["title"] = $_POST['title'];//漫展名称
		$data["privurl"] = $_POST['url'];//个性域名
		$data["cityname"] = $_POST['cityname'];//城市名
		$data["lng"] = $_POST['lng'];//地图经度
		$data["lat"] = $_POST['lat'];//地图纬度
		$data["address"] = $_POST['address'];//详细地址

		$data["starttime"] = strtotime($_POST['startDate']);//开始时间
		$data["closetime"] = strtotime($_POST['endDate']);//结束时间

 		$data["pricetype"] = $_POST['fee_opt'];//票价类型
 		$data["price"] = $_POST['price'];//票价

		$data["tickettype"] = $_POST['ticket'];//订票方式
		$data["netticketaddress"] = $_POST['net_sell'];//网络售票地址
		$data["content"] = $_POST['content'];//简介

		$data["faceimg"] = $_POST['faceImg'];//封面图片地址id
		//var_dump($data);

		$idNum = $Articlecontent->add($data);
		if($idNum>0){
			//添加成功,跳转到列表页
			$this->redirect('MzInfo/mzList');
		}
 	}
}
?>