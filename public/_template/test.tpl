<div class="col-6">
    <form action="ExchangeGestion-addPlanning" method="POST">
        <div class="mb-3">
            <label for="jouer_semaine">Jouer semaine</label>
            <select class="form-control" id="jouer_semaine" name="jouer_semaine">
                <option value="">Sélectionner une valeur</option>
                <option value='Lundi'>Lundi</option>
                <option value='Mardi'>Mardi</option>
                <option value='Mercredi'>Mercredi</option>
                <option value='Jeudi'>Jeudi</option>
                <option value='Vendredi'>Vendredi</option>
                <option value='Samedi'>Samedi</option>
                <option value='Dimanche'>Dimanche</option>
            </select>
            <div class="mb-3">
                <label for="heure_debut">Heure debut</label>
                <input class="form-control" id="heure_debut" type="time" name="heure_debut">
            </div>
            <div class="mb-3">
                <label for="heure_fin">Heure fin</label>
                <input class="form-control" id="heure_fin" type="time" name="heure_fin">
            </div><button type="submit">Envoyer</button>
    </form>
</div>