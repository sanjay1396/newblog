<?php
class LikeController extends Controller {

    public function actionCreate() {
            if(isset($_POST['Like'])) {
            $like = Like::create($_POST['Like']);
            if(!$like->errors) {
                $this->Success(array());
                echo "Liked Successfully";
            } else {
                $this->Error($this->ModelErrorMessage($like));
            }
        } else {
            $this->Error('Please Send the like data first...!!');
        }
    }
}