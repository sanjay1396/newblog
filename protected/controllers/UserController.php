<?php
class UserController extends Controller {

	public $_user;

	public function filters() {
		return array(
			'checkUser + login, view, comments, likes, delete'
			);
	}

	public function filterCheckUser($filterChain) {
		if(!isset($_GET['id'])) {
			$this->Error('Invalid data...!!');
		}
		else {
			$this->_user = User::model()->active()->findByPk($_GET['id']);
			if(!$this->_user) $this->Error("Invalid Data...!!");
		}
		$filterChain->run();
	}

	public function actionCreate() {
		//var_dump($_POST);
		//exit();
		if(isset($_POST['User'])) {
			$user = User::create($_POST['User']);
			if(!$user->errors) {
				$this->Success(array('user_id'=>$user->id));
			} else {
				$this->Error($this->ModelErrorMessage($user));
			}
		} else {
			$this->Error('Enter the user data first...!!');
		}
	}

	public function actionLogin($id) {
		if(!$this->_user){
			echo "Account doesn't exist";
		}
		else $this->Success(array('You are Successfully loggedin into'=>$this->_user->id,'user_name'=>$this->_user->name));
	}

	public function actionView($id) {
		if(!$this->_user) {
			echo "Account doesn't exist";
		}
		else $this->Success(array('user_id'=>$this->_user->id,'user_name'=>$this->_user->name,'user_email'=>$this->_user->email,'user_password'=>$this->_user->password));
	}

	public function actionComments($id) {
		if(!$this->_user){
			$this->Error('The id you have entered is invalid');
		}
		else {
			$user_comments = array();
			$comments = $this->_user->comments;
			foreach ($comments as $comment) {
				$user_comments[] = array('comment_id'=>$comment->id,'comment_user_id'=>$comment->user_id,'comment_post_id'=>$comment->post_id,'comment_content'=>$comment->content);
			}
			$this->Success(array('users_comments'=>$user_comments));
		}
	}

	public function actionSearch($str) {
		$posts = Post::model()->findAll(array('condition'=>"content LIKE :str", 'params'=>array('str'=>"%$str%")));
		if(!$posts){
			$this->Error('Invalid data...!');
		}
		else {
			$posts_data = array();
			foreach ($posts as $post) {
			$posts_data[] = array('id'=>$post->id, 'content'=>$post->content);
			}
			$this->Success(array('posts_data'=>$posts_data));
		}
	}

	public function actionLikes($id) {
		if(!$this->_user){
			$this->Error('The id you have entered is invalid');
		}
		else {
			$user_likes = array();
		$likes = $this->_user->likes;
		foreach ($likes as $like){
			$user_likes[] = array('like_id'=>$like->id,'like_user_id'=>$like->user_id,'like_post_id'=>$like->post_id);
		}
		$this->Success(array('users_likes'=>$user_likes));
		}
	}

	public function actionDelete($id) {
		//$user = User::model()->findByPk($id);
		$this->_user->status = 2;
		$this->_user->save();
		$this->Success(array('Successfully Deleted'));
	}

	public function actionRestore($id) {
		$user = User::model()->findByPk($id);
		$user->status = 1;
		$user->save();
		$this->Success(array('Successfully Restored'));
	}
}