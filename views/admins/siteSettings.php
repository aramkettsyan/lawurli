<?php

use dosamigos\fileupload\FileUpload;
use app\assets\AdminAsset;

AdminAsset::register($this);
?>

<div class="container">
    <div class="adminCont">
<h2>Logo</h2>
<br>
<div id="logo_container" >
    <?php $filename = \Yii::getAlias('@webroot') . '/images/' . $logo; ?>
    <?php if (is_file($filename)) { ?>
        <img src="<?php echo \Yii::getAlias('@web') . '/images/' . $logo; ?>"  style="height: 200px;width: 200px" alt="Logo" >
    <?php }else{ ?>
        <p>No logo uploaded yet!</p>
    <?php } ?>
</div>
<br>
<div id="file-uploader">       
    <noscript>          
    <p>Please enable JavaScript to use file uploader.</p>
    <!-- or put a simple form for upload here -->
    </noscript>         
</div>
</div>
</div>

<script type="text/javascript">
    new qq.FileUploader({
        element: document.getElementById("file-uploader"),
        action: '/admins/upload-image',
        uploadButtonText: 'Upload image',
        multiple: false,
        sizeLimit: 5242880,
        allowedExtensions: ['png', 'jpg', 'jpeg', 'gif'],
        onComplete: function (id, fileName, responseJSON) {

            if (responseJSON.success) {
                $('#logo_container').html("<img src='/images/" + responseJSON.fileName + "' style='height: 200px;width: 200px'>");
            }
        }
    });
</script>

