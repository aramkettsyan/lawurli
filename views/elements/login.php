<?php

use yii\helpers\Html;
use app\assets\UserAsset;
use yii\widgets\ActiveForm;
?>

<!-- login popup -->
<div id="login-popup" class="popupWrap popupSmall mfp-hide">
    <div class="popupTitle">
        <h5>Login</h5>
        <button class="mfp-close"></button>
    </div>
    <div class="popupCont">
        <?php
//        echo \Yii::$app->getSession()->getFlash('success');
//        echo \Yii::$app->getSession()->getFlash('warning');
        ?>
        <?php
        $form = ActiveForm::begin([
                    'id' => 'login-form'
        ]);
        ?>



        <?=
        $form->field($this->params['model'], 'email', [
            'template' => "{input} <i class='icon-email-streamline'></i>
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
            'options' => [
                'class' => 'formRow frIconLeft'
    ]])->textInput(['class' => 'formControl', 'placeholder' => 'Email']);
        ?>

        <?=
        $form->field($this->params['model'], 'password', [
            'template' => "{input} <i class='icon-lock-streamline'></i>
                        <span class='inputError'>
                            <i class='icon-warning-alt'></i>
                            <span>{error}</span>
                        </span>",
            'options' => [
                'class' => 'formRow frIconLeft'
    ]])->passwordInput(['class' => 'formControl', 'placeholder' => 'Password']);
        ?>

        <div class="remMeForgPass clearAfter">
            <?=
            $form->field($this->params['model'], 'rememberMe', ['options' => [
                    'class' => 'checkbox'
        ]])->checkbox(['label' => 'Remember me!'])
            ?>
            <div class="forgPass">
                <a class="textBtn popupBtn" href="#forgpass-popup" >Forgot password?</a>
            </div>
        </div>
        <?= Html::submitButton('Login', ['class' => 'btn defBtn']) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<!-- sign up popup -->

<?php
if (Yii::$app->getSession()->hasFlash('passwordResend')) {
    $passwordResend = Yii::$app->getSession()->getFlash('passwordResend');
} else {
    $passwordResend = '';
}
?>

<script type="text/javascript">
    $(document).ready(function(){
        var passwordResendError = '<?= $passwordResend ?>';
        $('#loginform-password').parent().find('.help-block').html(passwordResendError);
    }
    );
</script>
