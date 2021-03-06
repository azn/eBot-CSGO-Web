<h3><?php echo __("Création d'un serveur"); ?></h3>
<hr/>
<style>
    label.valid {
        width: 24px;
        height: 24px;
        background: url(/img/valid.png) center center no-repeat;
        display: inline-block;
        text-indent: -9999px;
    }
    label.error {
        font-weight: bold;
        color: red;
        padding: 2px 8px;
        margin-top: 2px;
    }
</style>

<script>
    $(function() { 
        $('#form-match').validate(
        {
            rules: {
                "servers[ip]": {
                    minlength: 5,
                    required: true
                }, "servers[rcon]": {
                    minlength: 1,
                    required: true
                }, "servers[hostname]": {
                    minlength: 1,
                    required: true
                }
            },
            highlight: function(label) {
                $(label).closest('.control-group').addClass('error');
            },
            success: function(label) {
                label
                .text('OK!').addClass('valid')
                .closest('.control-group').addClass('success');
            }
        });
    });
        
</script>
<form class="form-horizontal" id="form-match" method="post" action="<?php echo url_for("servers_create"); ?>">
    <?php echo $form->renderHiddenFields(); ?>
    <div class="well">
        <?php foreach ($form as $widget): ?>
            <?php if ($widget->isHidden()) continue; ?>
            <div class="control-group">
                <?php echo $widget->renderLabel(null, array("class" => "control-label")); ?>
                <div class="controls">
                    <?php echo $widget->render(); ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="control-group">
            <div class="controls">
                <input type="submit" class="btn btn-primary" value="<?php echo __("Créer le serveur"); ?>"/>
            </div>
        </div>
    </div>
    
    <div class="alert alert-info">
        <p><?php echo __("Pour créer plusieurs serveurs en une fois, vous pouvez renseigner l'adresse du serveur dans plusieurs format:"); ?></p>
        <ul>
            <li><b>172.16.100.101-103:27015</b> : <?php echo __("générera 3 serveurs avec comme ip 172.16.100.101:27015, 172.16.100.102:27015, 172.16.100.103:27015"); ?></li>
            <li><b>172.16.100.101:27015-27025-27035</b> : <?php echo __("générera 4 serveurs avec comme ip 172.16.100.101:27015, 172.16.100.101:27025, 172.16.100.101:27035"); ?></li>
            <li><b>172.16.100.101-103:27015-27025-27035</b> : <?php echo __("générera 9 serveurs avec comme ip 172.16.100.101:27015 > 172.16.100.101:27035, etc ..."); ?></li>
        </ul>
    </div>
</form>
