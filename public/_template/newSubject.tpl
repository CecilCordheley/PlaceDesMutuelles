<form action="addSujet" method="POST" class="offset-1 col-10">
    <div class="header">
        <h2>Nouveau Sujet</h2>
        <div class="mb-3 row">
            <label for="sujet_title" class="col-sm-2 col-form-label">Titre du sujet</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="TITRE_SUJET" id="sujet_title">
            </div>
        </div>
        <div class="mb-3 row">
            <label for="sujet_url" class="col-sm-2 col-form-label">Format URL</label>
            <div class="col-sm-10">
                <input type="text" class="form-control form-control-sm" name="URL_SUJET" id="sujet_url" readonly>
            </div>
        </div>
        <div class="mb-3 row">
            <label for="ID_THEME" class="col-sm-2 col-form-label">Catégories</label>
            <div class="col-sm-10">
                <select class="form-control" name="ID_THEME">
                    {LOOP:Theme}
                    <option value="{#ID_THEME#}">{#LIB_THEME#}</option>
                    {/LOOP}
                </select>
            </div>
        </div>
        <div class="mb-3 row">
            <button class="btn btn-primary">Créer le sujet</button>
        </div>
    </div>
</form>