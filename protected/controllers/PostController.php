<?php
class PostController extends Controller {
	
	public $_post;

	public function filters() {
		return array(
			'checkUser + view, comments, likes,delete,update'
			);
	}

	public function filterCheckUser($filterChain) {
		if(!isset($_GET['id'])) {
			$this->Error('Invalid data...!!');
		}
		else {
			$this->_post = Post::model()->active()->findByPk($_GET['id']);
			if(!$this->_post) $this->Error("Invalid Data...!!");
		}
		$filterChain->run();
	}

	public function actionCreate() {
		if(isset($_POST['Post'])) {
			$post = Post::create($_POST['Post']);
			if(!$post->errors) {
				$this->Success(array('post_id'=>$post->id));
			} else {
				$this->Error($this->ModelErrorMessage($post));
			}
		} else {
			$this->Error('Please send post data!');
		}
	}

	public function actionComments($id) {
		$post = Post::model()->findbyPK($id);
		if(!$this->_post){
			$this->Error('The id you have entered is invalid');
		}
		else {
			$post_comments = array();
			$comments = $post->comments;
			foreach ($this->_post->comments as $comment) {
				$post_comments[] = array('comment_id'=>$comment->id,'comment_post_id'=>$comment->post_id,'comment_user_id'=>$comment->user_id,'comment_content'=>$comment->content);
			}
			$this->Success(array('post_comments'=>$post_comments));
		}
	}

	public function actionLikes($id) {
		$post = Post::model()->findByPK($id);
		if(!$this->_post){
			$this->Error('The id you have entered is invalid');
		}
		else {
			$post_likes = array();
			$likes = $post->likes;
			foreach ($this->_post->likes as $like){
				$post_likes[] = array('like_post_id'=>$like->post_id,'like_user_id'=>$like->user_id);
			}
			$this->Success(array('posts_likes'=>$post_likes));
		}
	}
	
	public function actionNews($id) {
		$posts = Post::model()->findAllByAttributes(array('user_id'=>$id));
		if(!$posts){
			$this->Error('The id you have entered is invalid');
		}
		else {
			$posts_data = array();
			foreach ($posts as $post) {
				$posts_data[] = array('id'=>$post->id, 'content'=>$post->content);
			}
			$this->Success(array('posts_news'=>$posts_data));
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

	public function actionView($id){
		if(!$this->_post){
			$this->Error('No post with such id');
		}
		else {
			$this->Success(array('post_id'=>$this->_post->id,'post_title'=>$this->_post->title,'post_content'=>$this->_post->content));
		}
	}

	public function actionDelete($id){
		$this->_post->status = 2;
		$this->_post->save();
		$this->Success(array('successfully Deleted the post with th id'=>$id));
	}
	public function actionrestore($id){
		$post = Post::model()->findByPk($id);
		$post->status = 1;
		$post->save();
		$this->Success(array('successfully Restored the Post with id'=>$id));
	}

	public function actionUpdate($str, $id){
		$temp = Post::model();
		$temp->content = $this->_post->content;
		$this->_post->content = $str;
		$this->Success(array('successfully updated the post content from'=>$temp->content,'to '=>$this->_post->content));
	}
}