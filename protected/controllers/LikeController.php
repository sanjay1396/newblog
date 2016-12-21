<?php
class LikeController extends Controller {

    public function actionCreate() {
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
       //echo "No. of Likes = ".count($counts)."  "."<br>";
       //echo CJSON::encode(array('status'=>'SUCCESS','users_data'=>$users_data,));
        $this->Success(array('no_of_likes'=>count($counts),'users_data'=>$users_data));
        }
   }
}