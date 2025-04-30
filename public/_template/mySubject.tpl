<h2>Mes Sujets</h2>
<main class="row">
    {LOOP:MySubject}
    <div class="card" style="width: 18rem; margin:5px;">
        <div class="card-body">
          <h5 class="card-title">{#LIB_SUJET#}</h5>
          <h6 class="card-subtitle mb-2 text-body-secondary">Card subtitle</h6>
          <ul>
            <li>Ouvert : {#DATE_SUJET#}</li>
            <li>Cloture : {#DATE_CLOTURE#}</li>
            <li>Nombre de message : {#NB_MESSAGE#}</li>
            <li>Dernier message {#LAST_MESSAGE#}</li>
          </ul>
          <p class="card-text"></p>
          <a class="btn btn-primary" title="Voir le sujet" href="{:racine}sujet-{#ID_SUJET#}-{#URL_SUJET#}.html"><i class="fa-regular fa-eye"></i></a>
          <a class="btn btn-danger" title="Clore ce sujet" href="closeSubject-{#ID_SUJET#}"><i class="fa-regular fa-rectangle-xmark"></i></a>
        </div>
      </div>
    {/LOOP}
</main>