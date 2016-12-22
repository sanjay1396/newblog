<?php
class LikeController extends Controller {

/*	public function actionCreate() {
		if(isset($_POST['Like'])) {
			$like = Like::create($_POST['Like']);
			if(!$like->errors) {
				$this->Success(array('Liked Successfully'));
				//echo "Liked Successfully";
			} else {
				$this->Error($this->ModelErrorMessage($like));
			}
		} else {
			$this->Error('Please Send the like data first...!!');
		}
	}
*/
	 public function actionCreate() {
       if(isset($_POST['Like'])) {
           $existing_like = Like::model()->findByAttributes(array('user_id'=>$_POST['Like']['user_id'],'post_id'=>$_POST['Like']['post_id']));
           if(!$existing_like)
           {
               $like = Like::create($_POST['Like']);
               if(!$like->errors) {
                   $this->Success(array('post_id'=>$like->post_id,'user_id'=>$like->user_id));
               }
           }
           else {

               if($existing_like->status == 1) {

                   $existing_like->deactivate();
                   $this->Success(array('success'=>"Like removed."));
               }
               else if($existing_like->status == 2) {

                   $existing_like->activate();
                   $this->Success(array('success'=>"Liked."));
               }  
           }
       }
       else {
           $this->Error('Please Send the like data first...!!');
       }
   }

	public function actionCount($id){
		$counts = Like::model()->findAllByAttributes(array('post_id'=>$id));
		if(!$counts){
			$this->Error( 'The id you have entered is invalid' );
		}
		else {
			$users_data = array();
			foreach ($counts as $count) {
				$users_data[] = array('user_id'=>$count->user_id,'user_name'=>$count->user->name);
			}
			$this->Success(array('no_of_likes'=>count($counts),'users_data'=>$users_data));
		}
	}
}