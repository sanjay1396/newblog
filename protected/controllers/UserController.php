<?php
class UserController extends Controller {

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
		$user = User::model()->findbyPK($id);
		if(!$user){
			echo "Account doesn't exist";
		}
		else $this->Success(array('user_id'=>$user->id));
	}

	public function actionView($id) {
		$user = User::model()->findbyPK($id);
		if(!$user) {
			echo "Account doesn't exist";
		}
		else $this->Success(array('user_id'=>$user->id,'user_name'=>$user->name,'user_email'=>$user->email,'user_password'=>$user->password));
	}

	public function actionComments($id) {
			$user = User::model()->findbyPK($id);
			if(!$user){
				$this->Error('The id you have entered is invalid');
			}
			else {
				$user_comments = array();
			$comments = $user->comments;
			foreach ($comments as $comment) {
				$user_comments[] = array('comment_id'=>$comment->id,'comment_user_id'=>$comment->user_id,'comment_post_id'=>$comment->post_id,'comment_content'=>$comment->content);
			}
			$this->Success(array('users_comments'=>$user_comments));
			}
	}

	public function actionLikes($id) {
			$users = User::model()->findByPK($id);
			$users_likes = array();
				$likes = $users->likes;
				foreach ($likes as $like){
					$users_likes[] = array('like_id'=>$like->id,'like_user_id'=>$like->user_id,'like_post_id'=>$like->post_id);
				}
			$this->Success(array('users_likes'=>$users_likes));
	}

	public function actionSearch($str) {
		
		$posts = Post::model()->findAll(array('condition'=>"content LIKE :str", 'params'=>array('str'=>"%$str%")));
		//echo count($posts);
		$posts_data = array();
	   foreach ($posts as $post) {
		   $posts_data[] = array('id'=>$post->id, 'content'=>$post->content);
	   }
	   $this->Success(array('posts_data'=>$posts_data));
   }
}