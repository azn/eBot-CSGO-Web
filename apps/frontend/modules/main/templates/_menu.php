<?php $current_route = sfContext::getInstance()->getRouting()->getCurrentRouteName(); ?>
<div class="span2">
    <div class="well sidebar-nav">
        <script>
            function goToMatch() {
                var id = $("#match_id_go").val();
                if (id > 0) 
                    document.location.href = "<?php echo url_for("@matchs_view?id="); ?>"+id;
            };
        </script>
        <ul class="nav nav-list">
            <li class="nav-header"><?php echo __("Menu principal"); ?></li>
            <li <?php if ($current_route == "homepage") echo 'class="active"'; ?>><a href="<?php echo url_for("homepage"); ?>"><?php echo __("Accueil"); ?></a></li>
            <li <?php if (preg_match('!^stats_index$!', $current_route)) echo 'class="active"'; ?>><a href="<?php echo url_for("stats/index"); ?>"><?php echo __("Statistiques"); ?></a></li>
            <li <?php if (preg_match('!^ingame!', $current_route)) echo 'class="active"'; ?>><a href="<?php echo url_for("main/ingame"); ?>"><?php echo __("Aide ingame"); ?></a></li>
            <li class="nav-header"><?php echo __("Menu matchs"); ?></li>
            <li <?php if (preg_match('!^matchs_current!', $current_route)) echo 'class="active"'; ?>><a href="<?php echo url_for("@matchs_current_page?page=1"); ?>"><?php echo __("Matchs en cours"); ?> <span class="badge badge-info"><?php echo $nbMatchs; ?></span></a></li>
            <li <?php if (preg_match('!^matchs_archived!', $current_route)) echo 'class="active"'; ?>><a href="<?php echo url_for("@matchs_archived_page?page=1"); ?>"><?php echo __("Matchs archivés"); ?></a></li>
            <li>
                <div style="margin-top: 5px; margin-bottom: 5px;" class="input-append">
                    <input class="span2" id="match_id_go" size="16" type="text">
                    <button class="btn" type="button" onclick="goToMatch();">Go!</button>
                </div>
            </li>
            <li class="nav-header"><?php echo __("Menu statistiques"); ?></li>
            <li <?php if (preg_match('!^global!', $current_route)) echo 'class="active"'; ?>><a href="<?php echo url_for("stats/global"); ?>"><?php echo __("Statistique globale"); ?></a></li>
            <li <?php if (preg_match('!^stats_maps!', $current_route)) echo 'class="active"'; ?>><a href="<?php echo url_for("stats_maps"); ?>"><?php echo __("Statistique par maps"); ?></a></li>
            <li <?php if (preg_match('!^stats_weapons!', $current_route)) echo 'class="active"'; ?>><a href="<?php echo url_for("stats_weapons"); ?>"><?php echo __("Statistique par armes"); ?></a></li>
        </ul>
    </div>
</div>