<div class="container">
    <!--Gestion des echanges-->
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link active" _target='Planning' aria-current="page" href="#">Plages Horaires</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" aria-current="page" _target='ExchangeList' href="#">Liste des echanges</a>
        </li>
    </ul>
    <!--Formulaire de gestion des plages horaires-->
    <div class="row" name="Planning">
        <div class="col-6">
            <ul class="list-group col-6">
                {LOOP:joursDisponibles}
                <li>
                    <button class="btn btn-day" data-day="{#JOUR#}" data-date="{#DATE#}">{#JOUR#}</button>
                    <a href="ExchangeGestion/delJour/{#DATE#}" class="btn btn-danger" data-day="{#JOUR#}"
                        data-date="{#DATE#}">supprimer</a>
                </li>
                {/LOOP}
            </ul>
        </div>
        <div class="col-6">
            {var:addForm|rawHTML}
        </div>
    </div>
    <div class="row" name="ExchangeList">

        <div class="col-5">
            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#exchangeHisto">Voir
                l'historique</button>
            <h4>liste des échanges en cours</h4>
            <ul>
                {LOOP:exchangeList}
                <li>{#LOGIN_USER#} -{#dateExchange#} {#HeureDebut#} /
                    {:IF {#NOW#}=1}
                    <a href="#" data-exchange="{#uuidExchange#}" class="btn btn-primary">Gérer cet échange</a>
                    {:ELSE:}
                    <span>A venir</span>
                    {:/IF}
                </li>
                {/LOOP}
            </ul>
        </div>
        <div id="exchangeSee" class="modal fade" tabindex="-1" aria-labelledby="exchangeSee" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modeal-header">
                        <h4>Echanges</h4>
                    </div>
                    <div class="modal-body exchangeContent">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="exchangeHisto" tabindex="-1" class="modal fade" aria-labelledby="exchangeHisto" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Historique</h4>
                    </div>
                    <div class="modal-body" style="height: 500px; overflow: auto;">
                        <table class="table historique">
                            <thead>
                                <tr>
                                    <th>PSEUDO</th>
                                    <th>JOUR</th>
                                    <th>HEURE DEBUT</th>
                                    <th>STATUT</th>
                                    <th>NB MESSAGES</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                {LOOP:histoExchange}
                                <tr>
                                    <td>{#PSEUDO_USER#}</td>
                                    <td>{#JOUR#}</td>
                                    <td>{#HEURE#}</td>
                                    <td>{#STATUT#}</td>
                                    <td>{#NBMESSAGE#}</td>
                                    <td>
                                        <a href="#" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#exchangeSee">Voir</a>
                                    </td>
                                </tr>
                                {/LOOP}
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <a href="./ExchangeGestion-purge" class="btn btn-danger">Purger</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4" name="chat" data-user="{var:user.ID_USER}">
            <h5>Echange : <span>UUID</span></h5>
            <div id="currentChat"></div>
            <input type="text" name="chatMessage" placeholder="votre message"><button id="sendMessage"
                class="btn btn-primary">Envoyer</button>
        </div>
    </div>
</div>
<script>
    $('#sendMessage').click(function () {
        let id = $('[name=chat]').attr("data-user");
        let uuid = $('[name=chat]>h5>span').html();
        sendMessage(id, uuid, $('[name=chatMessage]').val(), () => {
            $('[name=chatMessage]').val("");
        });
    })
    function exchangeListener(data, user) {
        let container = document.querySelector("#currentChat");
        container.innerHTML = "";
        data.forEach(msg => {
            let mess = document.createElement("div");
            if (user == msg.utilisateur) {
                mess.classList.add("current");
            } else {
                mess.classList.add("interlocuteur");
            }
            mess.innerHTML = "<span>" + msg["dateIntMessage"].split(' ')[1] + "</span>";
            mess.innerHTML += "<span>" + msg["contentIntMessage"] + "</span>";
            container.appendChild(mess);

        })
    }
</script>