<h2>Tout les sujets</h2>
<div class="accordion" id="accordionTopic">
    {LOOP:sujet}
    <div class="accordion-item">
        <h3 class="accordion-header {:IF {#DATE_CLOTURE#}!}closed{:/IF}">
          <button class="accordion-button " type="button" data-bs-toggle="collapse" data-bs-target="#collapse{#ID_SUJET#}" aria-expanded="true" aria-controls="collapseOne">
            {#LIB_SUJET#}
          </button>
        </h3>
        <div id="collapse{#ID_SUJET#}" class="accordion-collapse collapse " data-bs-parent="#accordionTopic">
          <div class="accordion-body">
            <div class="card" style="width: 18rem;">
                <div class="card-header">
                 ouvert par {#LOGIN_UTILISATEUR#}
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Theme : {#THEME_NAME#}</li>
                  <li class="list-group-item">
                    <span>Nombre de messages : </span>
                    <span>{#NBMESSAGE#}</span>
                  </li>
                 
                  {:IF {#DATE_CLOTURE#}!}
                  <li class="list-group-item">{#DATE_CLOTURE#}</li>
                  {:/IF}
                </ul>
              </div>
              <div class="lastMessage">
                <span>{#LAST_MESSAGE_DATE#}</span>
                <span>{#LAST_MESSAGE_USER#}</span>
                <p>{#LAST_MESSAGE_CONTENT#}</p>
                <a href="sujet-{#ID_SUJET#}-{#URL_SUJET#}.html">Voir ce sujet</a>
              </div>
          </div>
        </div>
      </div>
    {/LOOP}
  </div>
  {:IF {var:isConnect}=1}
  <a class="btn btn-primary" href='NouveauSujet'>Créer un nouveau sujet</a>
  {:/IF}
