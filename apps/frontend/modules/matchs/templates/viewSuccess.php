<?php use_helper('Date') ?>

<h4>Match #<?php echo $match->getId(); ?> - <?php echo $match->getTeamA(); ?> vs <?php echo $match->getTeamB(); ?></h4>
<hr/>

<style>
    .bar_ct {
        background-image: url(/images/zoom/blue.png);
    }

    .bar_t {
        background-image: url(/images/zoom/red.png);
    }
</style>

<script>
    $(function() {
        $('#myTab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
            if ($(this).attr("href") == "#stats-match") {
                generateTimeLine(1);
            }
        })
        
        $(".needTips").tipsy({live:true});
        $(".needTips_S").tipsy({live:true, gravity: "s"});
    });
</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#home"><?php echo __("Information / Configuration du match"); ?></a></li>
    <li><a href="#stats-match"><?php echo __("Statistiques du match"); ?></a></li>
    <li><a href="#stats-players"><?php echo __("Statistiques des joueurs"); ?></a></li>
    <li><a href="#stats-weapon"><?php echo __("Statistiques des armes"); ?></a></li>
    <li><a href="#stats-killer-killed"><?php echo __("Tueur / tué"); ?></a></li>
    <?php if ($heatmap): ?>
        <li><a href="#heatmap"><?php echo __("Carte de chaleur"); ?></a></li>
    <?php endif; ?>
    <?php if (file_exists(sfConfig::get("app_log_match_admin") . "/match-" . $match->getId() . ".html")): ?>
        <li><a href="#logs">Logs</a></li>
    <?php endif; ?>
</ul>

<div class="tab-content" style="padding-bottom: 10px; margin-bottom: 20px;">
    <div class="tab-pane active" id="home">
        <table border="0" cellpadding="5" cellspacing="5" width="100%">
            <tr>
                <td width="50%">
                    <h5><i class="icon-wrench"></i> <?php echo __("Configuration du match"); ?></h5>

                    <table class="table">
                        <tr>
                            <th width="200"><?php echo __("Nom de la configuration"); ?></th>
                            <td><?php echo $match->getRules(); ?></td>
                        </tr>
                        <tr>
                            <th width="200"><?php echo __("MaxRound"); ?></th>
                            <td><?php echo $match->getMaxRound(); ?></td>
                        </tr>
                        <tr>
                            <th width="200"><?php echo __("Statut"); ?></th>
                            <td><?php echo $match->getStatusText(); ?></td>
                        </tr>
                        <tr>
                            <th width="200"><?php echo __("Maps"); ?></th>
                            <td><?php echo $match->getMap()->getMapName(); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __("Actif ?"); ?></th>
                            <td>
                                <?php if ($match->getEnable()): ?>
                                    <?php if ($match->getStatus() == Matchs::STATUS_STARTING): ?>
                                        <?php echo image_tag("/images/icons/flag_blue.png"); ?> démarrage
                                    <?php else: ?>
                                        <?php echo image_tag("/images/icons/flag_green.png"); ?> oui
                                    <?php endif; ?>
                                <?php else:
                                    ?>
                                    <?php echo image_tag("/images/icons/flag_red.png"); ?> non
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th width="200"><?php echo __("Jouer tous les rounds"); ?></th>
                            <td><?php echo image_tag("/images/icons/flag_" . ($match->getConfigFullScore() ? "green" : "red") . ".png"); ?></td>
                        </tr>
                        <tr>
                            <th width="200"><?php echo __("OverTime actif"); ?></th>
                            <td><?php echo image_tag("/images/icons/flag_" . ($match->getConfigOt() ? "green" : "red") . ".png"); ?></td>
                        </tr>
                        <tr>
                            <th width="200"><?php echo __("Knife Round"); ?></th>
                            <td><?php echo image_tag("/images/icons/flag_" . ($match->getConfigKnifeRound() ? "green" : "red") . ".png"); ?></td>
                        </tr>
                        <tr>
                            <th width="200"><?php echo __("Knife Switch Auto"); ?></th>
                            <td><?php echo image_tag("/images/icons/flag_" . ($match->getConfigSwitchAuto() ? "green" : "red") . ".png"); ?></td>
                        </tr>
                    </table>
                </td>
                <td width="50%" valign="top">
                    <?php
                    $score1 = $match->getScoreA();
                    $score2 = $match->getScoreB();

                    \ScoreColorUtils::colorForScore($score1, $score2);

                    $team1 = $match->getTeamA();
                    $team2 = $match->getTeamB();
                    if ($match->getMap() && $match->getMap()->exists()) {
                        \ScoreColorUtils::colorForMaps($match->getMap()->getCurrentSide(), $team1, $team2);
                    }
                    ?>
                    <h5><i class="icon-tasks"></i> <?php echo __("Information du match"); ?></h5>

                    <table class="table">
                        <tr>
                            <th width="200"><?php echo __("Date"); ?></th>
                            <td><?php echo format_date($match->getCreatedAt(), 'D') ?></td>
                        </tr>
                        <tr>
                            <th width="200"><?php echo __("Score"); ?></th>
                            <td><?php echo $team1; ?> (<?php echo $score1; ?>) - (<?php echo $score2; ?>) <?php echo $team2; ?></td>
                        </tr>
                        <tr>
                            <th width="200"><?php echo __("Nombre de round joué"); ?></th>
                            <td><?php echo $match->getScoreA() + $match->getScoreB(); ?></td>
                        </tr>
                        <?php if (sfConfig::get("app_mode") == "lan"): ?>
                            <tr>
                                <th width="200"><?php echo __("Serveur"); ?></th>
                                <td><?php echo $match->getIp(); ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <th width="200"><?php echo __("GO TV"); ?></th>
                            <td><?php echo $match->getServer()->getTvIp(); ?></td>
                        </tr>
                        <tr>
                            <th width="200"><?php echo __("Nombre de joueur"); ?></th>
                            <td>
                                <?php
                                $a = 0;
                                $b = 0;
                                $spec = 0;
                                foreach ($match->getPlayers() as $player) {
                                    if ($player->getTeam() == "other")
                                        $spec++;
                                    if ($player->getTeam() == "a")
                                        $a++;
                                    if ($player->getTeam() == "b")
                                        $b++;
                                }
                                echo $match->getPlayers()->count() . " - " . $team1 . " : " . $a . " - " . $team2 . " : " . $b . " - Spec: " . $spec;
                                ?>
                            </td>
                        </tr>
                    </table>

                    <h5><i class="icon-globe"></i> <?php echo __("Détails des scores"); ?></h5>

                    <table class="table">
                        <tr>
                            <td></td>
                            <td><?php echo $match->getTeamA(); ?></td>
                            <td><?php echo $match->getTeamB(); ?></td>
                        </tr>
                        <?php foreach ($match->getMap()->getMapsScore() as $score): ?>
                            <?php
                            $score1_side1 = $score->score1_side1;
                            $score1_side2 = $score->score1_side2;
                            $score2_side1 = $score->score2_side1;
                            $score2_side2 = $score->score2_side2;
                            ScoreColorUtils::colorForScore($score1_side1, $score2_side1);


                            ScoreColorUtils::colorForScore($score1_side2, $score2_side2);
                            ?>
                            <?php if ($score->getTypeScore() != "normal"): ?>
                                <tr>
                                    <th colspan="3">OverTime</th>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <th width="200">Premier side</th>
                                <td><?php echo $score1_side1; ?></td>
                                <td><?php echo $score2_side1; ?></td>
                            </tr>
                            <tr>
                                <th width="200">Second side</th>
                                <td><?php echo $score1_side2; ?></td>
                                <td><?php echo $score2_side2; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="tab-pane" id="stats-match">
        <?php include_partial("matchs/stats_match", array("match" => $match)); ?>
    </div>
    <div class="tab-pane" id="stats-players">
        <?php include_partial("matchs/stats_players", array("match" => $match)); ?>
    </div>
    <div class="tab-pane" id="stats-weapon">
        <?php include_partial("matchs/stats_weapon", array("match" => $match)); ?>
    </div>
    <div class="tab-pane" id="stats-killer-killed">
        <?php include_partial("matchs/stats_killer_killed", array("match" => $match)); ?>
    </div>
    <?php if (file_exists(sfConfig::get("app_log_match") . "/match-" . $match->getId() . ".html")): ?>
        <?php if ($match->getStatus() < Matchs::STATUS_END_MATCH): ?>
            <script>
                var autoscroll = true;
                function refreshLog () {
                    $.post("<?php echo url_for("matchs_logs", $match); ?>", {} , function (data) { 
                        if (data != "0") { 
                            if ($("#logmatch").html() != data) {
                                $("#logmatch").html(data); 
                                if (autoscroll) {
                                    var offset = $('#end').position().top;
                                    if (offset < 0 || offset > $("#logmatch").height()) {
                                        if (offset < 0)
                                            offset = $("#logmatch").scrollTop() + offset;
                                        $("#logmatch").animate({ scrollTop: offset }, 300);
                                    }
                                }
                            }
                        } }, "html");
                }
                                                                                        																																				                                                            				
                setInterval("refreshLog()",2000);
            </script>
        <?php endif; ?>
        <div class="tab-pane" id="logs">
            <div class="well" id="logs">
                <div style="height: 400px; overflow: auto;">
                    <div  id="logmatch">
                        <?php echo file_get_contents(sfConfig::get("app_log_match") . "/match-" . $match->getId() . ".html"); ?>
                    </div>
                    <div id="end"></div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($heatmap): ?>
        <div class="tab-pane" id="heatmap">
            <?php include_partial("matchs/stats_heatmap", array("match" => $match, "class_heatmap" => $class_heatmap)); ?>
        </div>
    <?php endif; ?>
</div>

