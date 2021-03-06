<?php
require_once '../config/config.php';
/*
* Check user is logined
*/
$admin_userid = $session->get_userdata('admin_userid');
if( !$admin_userid && ! ( $admin_userid > 0) ){
    $session->set_flashdata('msg','Please login to admin panel!');
    redirect('admin/login.php');
}
/*
* ### End login check
*/

$process = ( $input->get('option') == 'form' )  ? 'FORM' : 'LIST' ;
/*
* Save or Update data into database starts
*/
$id = $name = $address = $email = $phone = $password ="";
if( isset( $_POST['btnSave'] ) ){
    /*
    * Form validatin stars 
    */
    $form_validation->set_rules('name','Name','required','Please enter name');
    $form_validation->set_rules('email','Email','required|valid_email');
    $form_validation->set_rules('phone','Phone','required|valid_phone');
    $form_validation->set_rules('password','Password','required');
    $form_errors =  $form_validation->run();
    /*
    * ### End form validation
    */
    $id = (int) $input->post('id');
    $name = $input->post('name');
    $address = $input->post('address');
    $email = $input->post('email');
    $phone = $input->post('phone');
    $password = $input->post('password');
    if( count($form_errors) >0 ){
        $process = 'FORM';
        goto htmlView;
    }else{
        
        if ( $id > 0 ){
            $sql = "UPDATE `employees` SET `name`= '".$name."',`address`='".$address."',`email`='".$email."',`phone`='".$phone."',`password`='".$password."' WHERE id =".$id;
            if ( $db->execute($sql) ){
                $session->set_flashdata('msg',alert('Data updated successfully','success'));
            }else{
                $session->set_flashdata('msg',alert('Could n\'t updated the data !','danger'));
            }
        }else{
            $sql = "INSERT INTO `employees`( `name`, `address`, `email`, `phone`, `password`) VALUES ('".$name."','".$address."','".$email."','".$phone."','".$password."')";
            if ( $db->execute($sql) ){
                $session->set_flashdata('msg',alert('Data saved successfully','success'));
            }else{
                $session->set_flashdata('msg',alert('Could n\'t save the data !','danger'));
            }   
        }
        redirect('admin/employee.php');
    }
}
/*
*  ### END Save or Update data into database
*/

/*
*  Strats load data to edit
*/
$edit = (int)$input->get('edit');
$edit_rec = $db->get_row('employees',array('id' => $edit));
if ( $edit_rec ){
    $id = $edit_rec->id;
    $name = $edit_rec->name;
    $address = $edit_rec->address;
    $email = $edit_rec->email;
    $phone = $edit_rec->phone;
    $password = $edit_rec->password;
    $process = 'FORM';
}
/*
*  ### End load data to edit
*/

/*
*  Strats record delete
*/
$del = (int)$input->get('del');
if ( $del > 0 ) {
    $del_rec = $db->delete('employees',array('id' => $del));
    if ( $del_rec ){
        $session->set_flashdata('msg',alert('Data deleted successfully','success'));
    }else{
        $session->set_flashdata('msg',alert('Could n\'t delete the data !','danger'));
    }  
    redirect('admin/employee.php');
}
/*
*  ### End record delete
*/
if ($process != 'FORM'){
    $whrere = [];
    $order_by = array(
        'id' => 'ASC' , 
    );
    $rec_list = $db->get('employees', $whrere, $order_by);
}
/*
* Save or Update data into database ends
*/
htmlView:
include_once 'header.php';
?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Employee</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <?php  if ($process == 'FORM') {?>
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-default">
          <div class="panel-heading">
             <p><?= $id >0 ? 'Edit ' : 'Add ' ?> Employee </p>
          </div>
          <div class="panel-body">
            <div class="row">
             
              <div class="col-lg-6 col-lg-offset-2">
                <form class="form-horizontal" method="post" action="<?= base_url('admin/employee.php') ?>">
                  <input type="hidden" name="id" value="<?= $id ?>">
                  <div class="form-group">
                    <label class="control-label col-sm-2" >Name:</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" name="name" value="<?= $name ?>">
                      <?= form_error('name') ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-sm-2" >Address:</label>
                    <div class="col-sm-10">
                      <textarea  class="form-control" name="address"><?= $address ?></textarea>
                      <?= form_error('address') ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-sm-2" >Email:</label>
                    <div class="col-sm-10">
                      <input type="email" class="form-control" name="email" value="<?= $email ?>" >
                      <?= form_error('email') ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-sm-2" >Phone:</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" name="phone" value="<?= $phone ?>" >
                      <?= form_error('phone') ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-sm-2" >Password:</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" name="password" value="<?= $password ?>" >
                      <?= form_error('password') ?>
                    </div>
                  </div>

                  
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" name="btnSave" class="btn btn-success">Submit</button>
                      <a class="btn btn-default" href="<?= base_url('admin/employee.php') ?>">Cancel</a>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            <!-- /.row (nested) -->
          </div>
          <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
      </div>
      <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
<?php }else{ ?>
    <div  class="row" >
      <div class="col-lg-12 " style="margin-bottom: 10px">
        <a href="<?= base_url('admin/employee.php?option=form') ?>" class =" btn btn-info pull-right"><i class="fa fa-plus "></i> Add Employee</a>
      </div>
    </div>
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
             <?= $session->get_flashdata('msg') ?>
        </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            List of Employees 
          </div>
          <div class="panel-body">
            <div class="row">
              <div class="col-lg-12">
                <?php 
                if(count( $rec_list) == 0){
                    echo '<div class="jumbotron" style="padding:20px">
                      <h3 class="text-center" >No records found !</h3>
                    </div>';
                }else{ ?>
                  <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                      <thead>
                        <tr>
                            <th style="width: 4%">Sl.No</th>
                            <th style="width: 20%">Name</th>
                            <th style="width: 20%">Address</th>
                            <th style="width: 20%">Email</th>
                            <th style="width: 20%">Phone</th>
                            <th style="width: 8%"></th>
                            <th style="width: 8%"></th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php foreach ($rec_list as $key => $row) { ?>
                        <tr class="odd gradeX">
                          <td><?=  ($key+1) ?></td>
                          <td><?= $row['name']  ?></td>
                          <td><?= $row['address']  ?></td>
                          <td><?= $row['email']  ?></td>
                          <td><?= $row['phone']  ?></td>
                          <td><a href="<?= base_url('admin/employee.php?edit='.$row['id'] ) ?>" class="btn btn-sm btn-default"> <i class="fa fa-edit"></i> Edit </a></td>
                          <td><a onclick="if (confirm('Delete record ?')) { window.location.href='<?= base_url('admin/employee.php?del='.$row['id'] ) ?>';}" class="btn btn-sm btn-default"> <i class="fa fa-trash"></i> Delete </a></td>
                        </tr>
                      <?php } ?>
                        </tbody>
                    </table>
                <?php }?>         
              </div>
            </div>
            <!-- /.row (nested) -->
          </div>
          <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
      </div>
      <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
<?php } ?>
</div>
<!-- /#page-wrapper -->
<?php 
include_once 'footer.php';
?>
