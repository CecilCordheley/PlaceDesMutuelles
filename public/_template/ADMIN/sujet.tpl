<h2>Gestion des sujets</h2>
<div class="row">
    <div class="col-7">
        {:IF {var:user.TYPE_UTILISATEUR}!4}
        <h3>Liste des sujets de votre mutuelle</h3>
        {:/IF}
        <table class="table">
            <tr>
                <th>#</th>
                <th>LIBELLE</th>
                <th>UTILISATEUR</th>
                <th>{:IF {var:user.TYPE_UTILISATEUR}=4}ORGANISME{:/IF}</th>
                <th>VALIDE</th>
                <th colspan="3">Action</th>
            </tr>
            {LOOP:sujetList}
            <tr>
                <td>{#ID#}</td>
                <td>{#LIB_SUJET#}</td>
                <td>{#PSEUDO_USER#}</td>
                <td>{:IF {var:user.TYPE_UTILISATEUR}=4}{#NOM_ORGANISME#}{:/IF}</td>
                <td>{:IF {#DISPLAY_SUJET#}=1}
                    <i class="fa-solid fa-circle-check"></i>
                    {:ELSE:}
                    <i class="fa-solid fa-circle-xmark"></i>
                    {:/IF}
                </td>
                {:IF {var:droit.Lecture}=1}
                <td>
                    <a class="btn btn-primary" href="sujetGestion-{#ID#}"><i class="fa-solid fa-pen-to-square"></i></a>
                </td>
                {:/IF}
                {:IF {var:droit.Suppr}=1}
                <td>
                    <a class="btn btn-danger" href="sujetGestion-close_{#ID#}"><i
                            class="fa-solid fa-rectangle-xmark"></i></a>
                </td>
                {:/IF}
                <td>
                    <a data-bs-toggle="modal" data-bs-target="#SeeMessage" idSujet="{#ID#}" href="#"
                        class="btn btn-info">
                        <i class="fa-regular fa-comments"></i>
                    </a>
                </td>
            </tr>
            {/LOOP}
        </table>
    </div>
    <div class="col-5">
        <h3>Fiche du sujet</h3>
        <form method="POST" action="sujetGestion-update_{:GET name=id}">
            <div class="display" data-nb-el="5">
                <div class="row m-2">
                    <div class="col-3"><label for="updateID" class="form-label">#</label></div>
                    <div class="col-9"><input type="text" value="{var:UPDATE.ID_SUJET}" name="ID_SUJET"
                            class="form-control" id="updateID" placeholder="id du sujet" readonly></div>
                </div>
                <div class="row m-2">
                    <div class="col-3"><label for="updateLibelle" class="form-label">Libelle</label></div>
                    <div class="col-9"><input type="text" value="{var:UPDATE.LIB_SUJET}" name="LIB_SUJET"
                            class="form-control" id="updateLibelle" placeholder="Libellé"></div>
                </div>
                <div class="row m-2">
                    <div class="col-3"><label for="updateLibelle" class="form-label">URL</label></div>
                    <div class="col-9"><input type="text" value="{var:UPDATE.URL_SUJET}" name="URL_SUJET"
                            class="form-control" id="updateLibelle" placeholder="url" readonly></div>
                </div>
                <div class="row m-2">
                    <div class="col-3"><label for="updateLibelle" class="form-label">Utilisteur</label></div>
                    <div class="col-9"><input type="text" value="{var:UPDATE.PSEUDO_USER}" class="form-control"
                            id="updateLibelle" placeholder="Pseudo" readonly></div>
                </div>
                <div class="row m-2">
                    <div class="col-3"><label for="updateDateO" class="form-label">Date ouverture</label></div>
                    <div class="col-9"><input type="date" value="{var:UPDATE.DATE_SUJET}" class="form-control"
                            id="updateDateO" placeholder="Date d'ouverture" readonly></div>
                </div>
                <div class="row m-2">
                    <div class="col-3"><label for="updateDateCloture" class="form-label">Date de cloture</label></div>
                    <div class="col-9"><input type="date" name="DATE_CLOTURE" value="{var:UPDATE.DATE_CLOTURE}"
                            class="form-control" id="updateDateCloture" placeholder="Date de cloture"></div>
                </div>
                <div class="row m-2">
                    <div class="col-3"><label for="updateTheme" class="form-label">Theme</label></div>
                    <div class="col-9">
                        <select class="form-control" name="ID_THEME" id="updateTheme">
                            <option value="null">Séléctionnez un thème</option>
                            {LOOP:Theme}
                            <option value="{#ID_THEME#}" {#SELECTED#}>{#LIB_THEME#}</option>
                            {/LOOP}
                        </select>
                    </div>
                </div>
                <div class="row m-2">
                    <div class="col-3"><label for="updateDisplay" class="form-label">Display</label></div>
                    <div class="col-9"><input type="checkbox" name="DISPLAY_SUJET" class="form-check-input"
                            id="updateDisplay" {:IF {var:UPDATE.DISPLAY_SUJET}=1}checked{:/IF}></div>
                </div>
            </div>
            {:IF {var:droit.Update}=1}
            <div class="m-3">
                <button class="btn btn-primary">
                    Modifier
                </button>
            </div>
            {:/IF}
        </form>
    </div>
</div>
<div class="modal fade" id="SeeMessage" tabindex="-1" aria-labelledby="SeeMessageLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="SeeMessageLabel"></h1>
                <a id="export" class="btn btn-primary" href="export.php?getMessage&id=">
                    <i class="fa-solid fa-download"></i>
                </a>
                {:IF {var:user.TYPE_UTILISATEUR}!4}
                <a href="javascript:filterMessage({var:user.ID_ORGA})" title="voir les message de votre mutuelle"
                    class="btn btn-primary"><i class="fa-solid fa-filter"></i></a>
                {:/IF}
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <table class="table" id="messages">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Contenu</th>
                            <th>Utilisateur</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>