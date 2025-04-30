<h2>Evenement de {var:user.ORGANISME}</h2>
{LOOP:EventList}
<div class="container">
  <div class="row">
    <div class="col-4">
      <div class="card eventsCard">
        <div class="card-body">
          <h3 class="card-title">{#titreEvent#}
            <span>{#dateEvent#}</span>
            {:IF {var:user.TYPE_UTILISATEUR}=2}
            <a href="delEvent_{#idEvent#}">
              <i class="fa-solid fa-trash-can"></i>
            </a>
            {:/IF}
          </h3>
          <p class="card-text">{#descEvent#}</p>
        </div>
      </div>
    </div>
    {/LOOP}
    {:IF {var:user.TYPE_UTILISATEUR}=2}
    <div class="col-4 addEvent">
      <a href="#" data-bs-toggle="modal" data-bs-target="#AddEvent">
        <i class="fa-solid fa-plus"></i>
      </a>
    </div>
    {:/IF}
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="AddEvent" tabindex="-1" aria-labelledby="AddEventLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title fs-5" id="AddEventLabel">Ajouter un évèvement</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addEventForm" action="addEvent" method="POST">
          <div class="compoment">
            <label class="form-label" for="titleEvent">Titre de l'évenement</label>
            <input class="form-control" name="titleEvent" type="text">
          </div>
          <div class="compoment">
            <label class="form-label" for="DescEvent">Description</label>
            <textarea class="form-control" name="descEvent" id="DescEvent"></textarea>
          </div>
          <div class="compoment">
            <label class="form-label" for="dateEvent">Date de l'évèvement</label>
            <input class="form-control" name="dateEvent" type="date">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" id="addEventTrigger" class="btn btn-primary">Valider</button>
      </div>
    </div>
  </div>
</div>