<?php
class CommentController extends Controller {

    public function actionCreate() {
        //var_dump($_POST);
        //exit();
        if(isset($_POST['Comment'])) {
            $comment = Comment::create($_POST['Comment']);
            if(!$comment->errors) {
                $this->Success(array('comment_id'=>$comment->id));
            } else {
                $this->Error($this->ModelErrorMessage($comment));
            }
        } else {
            $this->Error('Please send the comment data first...!!');
        }
    }

    public function actionCount($id){
        $counts = Comment::model()->findAllByAttributes(array('post_id'=>$id));
        $users_data = array();
        foreach ($counts as $count) {
           $users_data[] = array('user_id'=>$count->user_id,'user_name'=>$count->user->name);
        }
       echo "No. of Comments = ".count($counts)."  "."<br>";
       echo CJSON::encode(array('status'=>'SUCCESS',

               'users_data'=>$users_data,
           ));
   }

   public function actionTopComments($id){

       $comments = Comment::model()->findAll(array('condition'=>"post_id = :post_id", 'params'=>array('post_id'=>$id), 'order'=>'created_at DESC', 'limit'=>5));
       $comments_data = array();
       foreach($comments as $comment){
           $comments_data[] = array('user_name'=>$comment->user->name, 'content'=>$comment->content);
       }
       echo CJSON::encode(array('status'=>'SUCCESS', 'Comments_information'=>$comments_data));
   }

}