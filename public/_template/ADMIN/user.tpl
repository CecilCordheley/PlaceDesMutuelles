<h2>Gestion des utilisateurs</h2>
<div class="row">
    <div class="col-12">
        <div class="btn-group" role="group" aria-label="Basic example">
            {:IF {var:droit.Create}=1}
            <a title="Créer un utilisateur" class="btn btn-primary" href="#" data-bs-toggle="modal"
                data-bs-target="#addUserModal">
                <i class="fa-solid fa-user-plus"></i>
            </a>
            {:/IF}
            <a class="btn btn-primary" href="#">
                <i class="fa-solid fa-upload"></i>
            </a>
            <a class="btn btn-primary" href="export.php?data=user">
                <i class="fa-solid fa-download"></i>
            </a>
            {:IF {var:droit.Update}=1}
            <a href="{:racine}UserGestion-UserCheck_0" class="btn btn-primary" title="Revérifier la validité">
                <i class="fa-solid fa-user-check"></i>
            </a>
            {:/IF}
        </div>
    </div>
    <div class="col-8">
        <h3>Liste des utilisateurs de votre mutuelle</h3>
        <table class="table">
            <tr>
                <th>#</th>
                <th>PSEUDO</th>
                <th>MAIL</th>
                <th>validité</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
            {LOOP:userList}
            <tr type_user="{#TYPE_UTILISATEUR#}">
                <td>{#ID_USER#}</td>
                <td>{#PSEUDO_USER#}</td>
                <td>{#MAIL_USER#}</td>
                <td>{:IF {#VALID_USER#}=1}
                    <i class="fa-solid fa-circle-check"></i>
                    {:ELSE:}
                    <i class="fa-solid fa-circle-xmark"></i>
                    {:/IF}
                </td>
                <td >{#USER_TYPE#}</td>
                <td>
                    <a title="Changer la validité" href="UserGestion-switch_{#ID_USER#}" class="btn btn-light">
                        <i class="fa-solid fa-repeat"></i>
                        {:IF {var:droit.Modarate}=1}
                    </a><a name="banUser" title="Bannir l'utilisateur" idUser="{#ID_USER#}" href="#" data-bs-toggle="modal" data-bs-target="#BannedUser" class="btn  btn-danger">
                        <i class="fa-solid fa-user-slash"></i>
                    </a><a name="timeOutUser" title="TimeOut l'utilisateur" idUser="{#ID_USER#}" href="UserGestion-timeout_{#ID_USER#}" class="btn btn-warning">
                        <i class="fa-solid fa-user-clock"></i>
                    </a>
                    {:/IF}
                    {:IF {var:droit.Lecture}=1}
                    <a title="Voir l'utilisateur" href="UserGestion-see_{#ID_USER#}" class="btn btn-info">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    {:/IF}
                </td>
            </tr>
            {/LOOP}
        </table>
    </div>
    <div class="col-4">
        {:IF {:GET name=id}!}
        <h3>Fiche de l'utilisateur <a data-bs-toggle="modal"  data-bs-target="#getStatModal" class="btn btn-primary" href="#">Voir les statut</a></h3>
        <form id="updateForm" method="POST" action="UserGestion-update_{:GET name=id}">
            <div class="mb-3">
                <label for="updatePseudo" class="form-label">Pseudo</label>
                <input type="text" value="{var:UPDATE.PSEUDO_USER}" name="PSEUDO_USER" class="form-control"
                    id="updatePseudo" placeholder="Pseudo">
            </div>
            <div class="mb-3">
                <label for="UpdateMail" class="form-label">Adresse mail</label>
                <input type="email" value="{var:UPDATE.MAIL_USER}" name="MAIL_USER" class="form-control" id="UpdateMail"
                    placeholder="name@example.com">
            </div>
            <div class="mb-3">
                <label for="UpdateArrive" class="form-label">Date d'arrivée</label>
                <input type="date" readonly value="{var:UPDATE.ARRIVE_UTILISATEUR}" name="ARRIVE_UTILISATEUR"
                    class="form-control" id="UpdateArrive" placeholder="12/99/2024">
            </div>
            <div class="mb-3">
                <label for="UpdateArrive" class="form-label">Date Validité</label>
                <input type="date" readonly value="{var:UPDATE.DATE_VALIDITE}" name="ARRIVE_UTILISATEUR"
                    class="form-control" id="UpdateValide" placeholder="12/99/2024">
            </div>
            <div class="mb-3">
                <label for="UpdateType" class="form-label">Type Utilisateur</label>
                <select name="TYPE_UTILISATEUR" class="form-control" id="UpdateArrive"
                    selected_value="{var:UPDATE.TYPE_UTILISATEUR}">
                    {LOOP:type_utilisateur}
                    <option value="{#idType_Utilisateur#}">{#LIBELLE_Type_Utilisateur#}</option>
                    {/LOOP}
                </select>
            </div>
            <div class="mb-3">
                <table class="table" id="USERDATA">{LOOP:DATAS}

                    <tr>
                        <th><input name="DATA_KEY[]" class="form-control" type="text" value="{#KEY#}"></th>
                        <td><input name="DATA_VALUE[]" class="form-control" type="text" value="{#VALUE#}"></td>

                        <td>
                            <a href="#" name="addData" class="btn btn-success">
                                <i class="fa-solid fa-plus"></i>
                            </a>
                        </td>
                        <td>
                            <a href="#" name="removeData" class="btn btn-danger">
                                <i class="fa-solid fa-minus"></i>
                            </a>
                        </td>
                    </tr>

                    {/LOOP}
                </table>
            </div>
            <div class="mb-3">
                <label for="updateOrganisme" class="form-label">Organisme</label>
                <input type="text" readonly value="{var:UPDATE.ORGANISME}" name="ORGANISME" class="form-control"
                    id="updateOrganisme" placeholder="Pseudo">
            </div>
            <div class="mb-3">
                <button class="btn btn-primary">Modifier</button>
            </div>
        </form>
        {:/IF}
    </div>
</div>
<!-- Modal add User-->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addUserModalLabel">Ajouter un utilisateur</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="UserGestion-add">
                    <div class="mb-3">
                        <label for="addPseudo" class="form-label">Pseudo</label>
                        <input type="text" value="{var:UPDATE.PSEUDO_USER}" name="PSEUDO_USER" class="form-control"
                            id="addPseudo" placeholder="Pseudo">
                    </div>
                    <div class="mb-3">
                        <label for="addMail" class="form-label">Adresse mail</label>
                        <input type="email" value="{var:UPDATE.MAIL_USER}" name="MAIL_USER" class="form-control"
                            id="addMail" placeholder="name@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="addType" class="form-label">Type Utilisateur</label>
                        <select name="TYPE_UTILISATEUR" class="form-control" id="addType"
                            selected_value="{var:UPDATE.TYPE_UTILISATEUR}">
                            {LOOP:Addtype_utilisateur}
                            <option value="{#idType_Utilisateur#}">{#LIBELLE_Type_Utilisateur#}</option>
                            {/LOOP}
                        </select>
                    </div>
                    <div class="mb-3">
                        <h4>User Data</h4>
                        <table class="table" id="addUSERDATA"></table>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Valider</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
               
            </div>
        </div>
    </div>
</div>
<!-- Modal get Statut-->
<div class="modal fade" id="getStatModal" tabindex="-1" aria-labelledby="getStatModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="getStatModalLabel">Statut de l'utilisateur</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Ici les statut de l'utilisateur {var:UPDATE.PSEUDO_USER}
                <table class="table">
                    <tr>
                        <th>LIBELLE</th>
                        <th>DATE</th>
                        <th>MOTIF</th>
                    </tr>
                    {LOOP:StatUser}
                    <tr>
                        <td>{#LIBELLE#}</td>
                        <td>{#DATE#}</td>
                        <td>{#MOTIF#}</td>
                    </tr>
                    {/LOOP}
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
               
            </div>
        </div>
    </div>
</div>
<!-- Modal bann user-->
<div class="modal fade" id="BannedUser" tabindex="-1" aria-labelledby="BannedUserModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addUserModalLabel">Bannir l'utilisateur</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <h4>Utilisateur : <span data-async="ID_USER"></span></h4>
               <label for="banComment">Motif du ban</label>
               <textarea class="form-control" name="comment" id="BanComment"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" id="triggerBan" class="btn btn-danger">Bannir</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
               
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('triggerBan').addEventListener('click',function(){
        const ban=banUser(document.querySelector("[data-async=ID_USER]").innerHTML);
        console.log(ban);
        if(ban!=false){
            $("#BannedUser").modal('hide');
        }else{
            alert("L'action ne peut être valide");
        }
        // r.then((response) => response.text())
        // .then((result) => {
        //     console.dir(r);
        // });
    })
    const setTypeUtilisateur=function(){
       let selected= document.querySelector("select[selected_value]");
       let val=selected.getAttribute("selected_value");
       selected.querySelector("option[value='"+val+"']").selected="selected";
    }
    const lines=document.querySelectorAll("[type_user]:not([type_user='1'])")
    lines.forEach(el=>{
        ["[name=banUser]","[name=timeOutUser]"].forEach(link=>{
           // debugger;
            let btn=el.querySelector(link);
            btn.setAttribute("disabled","disabled");
            btn.classList.add("disabled");
        })

        let btnBan=el.querySelector("[data-bs-target='#BannedUser']");
        btnBan.setAttribute("disabled","disabled");
        btnBan.classList.add("disabled");
    })
    const updateTable = (tableId) => {
        const tableElement = document.getElementById(tableId);
        if (tableElement === null) return;
      
        if (tableElement.innerHTML.trim() === "") {
            tableElement.innerHTML = `
            <tr>
                <td colspan="3">
                    <a href="#" name="addData" class="btn btn-success">
                        <i class="fa-solid fa-plus"></i>
                    </a>
                </td>
            </tr>`;
        }
    };
    document.getElementById("addType").addEventListener("change", function () {
        const tableElement = document.getElementById("addUSERDATA");
        const value = this.value;
        console.log(value);
        const behavior = {
            1: function () {
                tableElement.innerHTML = `
            <tr>
                <td colspan="3">
                    <a href="#" name="addData" class="btn btn-success">
                        <i class="fa-solid fa-plus"></i>
                    </a>
                </td>
            </tr>`;
            },
            2: function () {
                
                //Agent Nom, Prénom, droits [user,Sujet,Exchange]
                let lineNom = document.createElement("tr");
                lineNom.innerHTML = "<td>Nom : </td><td><input type='text' name='DATA_VALUE[nom]' class='form-control'></td>";
                tableElement.appendChild(lineNom);
                let linePrenom = document.createElement("tr");
                linePrenom.innerHTML = "<td>Prénom : </td><td><input type='text' name='DATA_VALUE[prenom]' class='form-control'></td>";
                tableElement.appendChild(linePrenom);
                let lineDroitUser = document.createElement("tr");
                lineDroitUser.innerHTML = "<td>Droit Utilisateurs : </td><td><input type='text' help=\"C:Creation,S:Suppresion,U:Update,L:Lecture,M:Mannage\" name='DATA_VALUE[droitUser]' class='form-control'></td>";
                tableElement.appendChild(lineDroitUser);
                let lineDroitSujet = document.createElement("tr");
                lineDroitSujet.innerHTML = "<td>Droit Sujets : </td><td><input type='text' help=\"C:Creation,S:Suppresion,U:Update,L:Lecture\" name='DATA_VALUE[droitSujet]' class='form-control'></td>";
                tableElement.appendChild(lineDroitSujet);
                let lineDroitExchange = document.createElement("tr");
                lineDroitExchange.innerHTML = "<td>Droit Echanges : </td><td><input type='text' help=\"E:Ecriture,L:Lecture,A:Admin\" name='DATA_VALUE[droitExchange]' class='form-control'></td>";
                tableElement.appendChild(lineDroitExchange);
                setHelp();
            }
        }
        tableElement.innerHTML = "";
        if (behavior[value] != undefined)
            behavior[value]();
    })
    updateTable("USERDATA");
    function setHelp() {
        document.querySelectorAll("[help]").forEach((element) => {
            element.addEventListener("focus", function () {
                const help = this.getAttribute("help");
                this.parentElement.innerHTML += `<span class="badge bg-primary">${help}</span>`;
            });
            element.addEventListener("focusout", function () {
                this.parentElement.children("span").remove();
            });
        });
    }
</script>