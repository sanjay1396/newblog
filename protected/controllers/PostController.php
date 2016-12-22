<?php
class PostController extends Controller {
	
	public $_post;

	public function filters() {
		return array(
			'checkPost + view, comments, likes, deactivate, update, topcomments'
		);
	}

	public function filterCheckPost($filterChain) {
		if(!isset($_GET['id'])) {
			$this->Error('Invalid data...!!');
		}
		else {
			$this->_post = Post::model()->active()->findByPk($_GET['id']);
			if(!$this->_post){
				$this->Error("Invalid Data...!!");
			} 
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
		if(!$this->_post){
			$this->Error('The id you have entered is invalid');
		}
		else {
			$no_of_comments=0;
			$post_comments = array();
			$comments = $this->_post->comments;
			foreach ($this->_post->comments('comments:active') as $comment) {
				$no_of_comments++;
				$post_comments[] = array('comment_id'=>$comment->id,'comment_user_id'=>$comment->user_id,'comment_content'=>$comment->content,'created_at'=>$comment->created_at);
			}
			$this->Success(array('no_of_comments'=>$no_of_comments,'post_comments'=>$post_comments));
		}
	}

	public function actionTopComments($id){
		$comments_data = array();
		foreach ($this->_post->comments(array('scopes'=>'active', 'order'=>'created_at DESC', 'limit'=>5)) as $comment) {
			$comments_data[] = array('user_name'=>$comment->user->name, 'content'=>$comment->content, 'created_at'=>$comment->created_at);
		}
		$this->Success(array('comments'=>$comments_data));
	}


	public function actionLikes($id) {
		if(!$this->_post){
			$this->Error('The id you have entered is invalid');
		}
		else {
			$no_of_likes=0;
			$post_likes = array();
			$likes = $this->_post->likes;
			foreach ($this->_post->likes('likes:active') as $like){
				$no_of_likes++;
				$post_likes[] = array('like_post_id'=>$like->post_id,'like_user_id'=>$like->user_id,'created_at'=>$like->created_at);
			}
			$this->Success(array('no_of_likes'=>$no_of_likes, 'posts_likes'=>$post_likes));
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

	public function actionCommentscount($id) {
		$comments_data = array();
		$number_of_comments = 0;
		foreach ($this->_post->comments('comments:active') as $comment) {
			$number_of_comments++;
			$comments_data[] = array('user_id'=>$comment->user_id, 'content'=>$comment->content);
		}
		$this->Success(array('number_of_comments'=> $number_of_comments,'comments'=>$comments_data));
	}

	public function actionView($id){
		if(!$this->_post){
			$this->Error('No post with such id');
		}
		else {
			$this->Success(array('post_id'=>$this->_post->id,'post_title'=>$this->_post->title,'post_content'=>$this->_post->content));
		}
	}

	public function actionDeactivate($id){
		$this->_post->deactivate();
		$this->Success(array('successfully Decactivated the post with th id'=>$id));
	}

	public function actionrestore($id){
		$post = Post::model()->findByPk($id);
		$post->restore();
		$this->Success(array('successfully Restored the Post with id'=>$id));
	}

	public function actionUpdate($str, $id){
		$temp = Post::model();
		$temp->content = $this->_post->content;
		$this->_post->content = $str;
		$this->Success(array('successfully updated the post content from'=>$temp->content,'to '=>$this->_post->content));
	}
}