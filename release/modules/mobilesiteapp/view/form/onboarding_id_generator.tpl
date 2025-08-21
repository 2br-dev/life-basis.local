<input name="onboarding_id" id="onboarding_id" value="{$elem.onboarding_id}" size="64" type="text" readonly>

<input onclick="generateID()" type="button" class="form-control btn btn-success" value="Сгенерировать ID"/>

<script type="text/javascript">
    function generateID() {
        let onBoardingId = "onboarding_id_";
        let rnd = Date.now().toString();
        while (rnd.length < 20) {
            rnd += Math.random().toString(36).substring(2);
        }
        onBoardingId += rnd;
    document.getElementById('onboarding_id').value=onBoardingId;
    }
</script>