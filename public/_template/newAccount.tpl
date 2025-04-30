<h2>Mon Compte</h2>
<form action="AddCompte" method="POST">
    <div class="mb-3 row">
        <label for="Pseudo" class="col-sm-2 col-form-label">Pseudo</label>
        <div class="col-sm-10">
          <input type="text" name="PSEUDO_USER" class="form-control" id="Pseudo" placeholder="Votre pseudo">
        </div>
      </div>
      <div class="mb-3 row">
        <label for="mail" class="col-sm-2 col-form-label">Email</label>
        <div class="col-sm-10">
          <input type="text" name="MAIL_USER" class="form-control" id="mail" placeholder="Votre email" >
        </div>
      </div>
      <div class="mb-3 row">
        <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
        <div class="col-sm-10">
          <input type="password" name="MDP_USER" class="form-control" id="inputPassword">
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-2 col-form-label" for="ORGA">Votre Mutuelle</label>
        <div class="col-sm-10">
            <select  class="form-control"  name="ID_ORGA" id="ORGA">
                <option value="NULL">Séléctionnez votre mutuelle</option>
                {LOOP:ORGA}
                <option value="{#ID_ORGA#}">{#NOM_ORGANISATION#}</option>
                {/LOOP}
            </select>
        </div>
    </div>
    <div class="flex-row-reverse mb-3 row">
        <button class="float-start col-sm-2 btn btn-primary">Créer mon compte</button>
    </div>
</form>