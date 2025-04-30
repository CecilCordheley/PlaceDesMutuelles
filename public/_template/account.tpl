<h2>Mon Compte</h2>

<div class="row">
  <div class="col-6">
    <form action="UpdateCompte" method="POST">
      <div class="mb-3 row">
        <label for="Pseudo" class="col-sm-2 col-form-label">Pseudo</label>
        <div class="col-sm-10">
          <input type="text" name="PSEUDO_USER" class="form-control" id="Pseudo" value="{var:user.PSEUDO_USER}">
        </div>
      </div>
      <div class="mb-3 row">
        <label for="mail" class="col-sm-2 col-form-label">Email</label>
        <div class="col-sm-10">
          <input type="text" name="MAIL_USER" class="form-control" id="mail" value="{var:user.MAIL_USER}">
        </div>
      </div>
      <div class="mb-3 row">
        <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
        <div class="col-sm-10">
          <input type="password" name="MDP_USER" class="form-control" id="inputPassword">
        </div>
      </div>
      <div class="mb-3 row">
        <label for="inputOrga" class="col-sm-2 col-form-label">Votre organisme</label>
        <div class="col-sm-10">
          <input type="text" name="ORGANISME" class="form-control" id="inputPassword" disabled readonly
            value="{var:user.ORGANISME}">
        </div>
      </div>
    </form>
  </div>
  <div class="col-6">
    <div class="mb-3 row">
      <label for="inputArrive" class="col-sm-2 col-form-label">Date d'arrivée</label>
      <div class="col-sm-10">
        <input type="text" name="ARRIVE" class="form-control" id="inputArrive" disabled readonly
          value="{var:user.ARRIVE_UTILISATEUR}">
      </div>
    </div>
    
    {LOOP:DATA_USER}
    <div class="mb-3 row">
      <label for="input{#KEY#}" class="col-sm-2 col-form-label">{#KEY#}</label>
      <div class="col-sm-10">
        <input type="text" name="{#KEY#}" class="form-control" id="input{#KEY#}" disabled readonly
        value="{#VALUE#}">
      </div>
    </div>
    {/LOOP}
    
  </div>
</div>