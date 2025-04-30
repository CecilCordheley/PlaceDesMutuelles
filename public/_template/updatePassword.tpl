<form name='UpdatePassWord' action="{:racine}root.php?act=udpatePassword" method="POST" class="offset-1 col-10">
  <div class="header">
    <h2>Connexion</h2>
  </div>
  <div class="mb-3 row">
    <label for="staticEmail" class="col-sm-2 col-form-label">Email</label>
    <div class="col-sm-10">
      <input type="mail" class="form-control" name="user_mail" id="login_mail">
    </div>
  </div>
  <div class="mb-3 row">
    <label for="inputPassword" class="col-sm-2 col-form-label">votre mot de passe</label>
    <div class="col-sm-10">
      <input type="password" name="user_mdp" class="form-control" id="inputPassword">
    </div>
  </div>
  <div class="mb-3 row">
    <label for="checkPassWord" class="col-sm-2 col-form-label">Repéter votre mot de passe</label>
    <div class="col-sm-10">
      <input type="password" name="user_check" class="form-control" id="checkPassWord">
    </div>
  </div>
  <div class="mb-3 row">
    <button class="col-4 offset-1 btn btn-primary">Modifier</button>
  </div>
</form>
<script>
    $('[type=password]').focus(function(){
        $(this).removeClass("border-danger");
    })
    $('form[name=UpdatePassWord]').submit(function(e){
        
        if($('#inputPassword').val()!=$('#checkPassWord').val()){
            _alert('Les mots de passe doivent être identique');
            $('[type=password]').addClass("border-danger");
            e.preventDefault();
        }else{
            return;
        }
        
    })
</script>