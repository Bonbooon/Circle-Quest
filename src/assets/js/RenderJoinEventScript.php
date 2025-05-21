<?php
function RenderJoinEventScript(bool $is_team_created, bool $has_applied, bool $has_pending_invitation)
{
    ob_start();
?>
    <script>
        const is_team_created = <?= $is_team_created ? 'true' : 'false' ?>;
        const has_applied = <?= $has_applied ? 'true' : 'false' ?>;
        const has_pending_invitation = <?= $has_pending_invitation ? 'true' : 'false' ?>;
        const btnUsers = document.getElementById('btn-users');
        const btnCircles = document.getElementById('btn-circles');
        const createTeamBtn = document.getElementById('create-team-btn');
        const applyBtn = document.getElementById('apply-btn');
        const selectedTeam = document.querySelector('input[name="team"]');

        function setButtonDisabled(button, isDisabled, bgClass) {
            button.disabled = isDisabled;
            button.classList.toggle('opacity-50', isDisabled);
            button.classList.toggle('cursor-not-allowed', isDisabled);
            button.classList.toggle(bgClass, !isDisabled);
        }

        function toggleButtonStyles(team) {
            if (team === 'users') {
                createTeamBtn.classList.add('hidden'); // Hide Create Team button
                if (has_applied) {
                    setButtonDisabled(btnCircles, true);
                } else {
                    setButtonDisabled(applyBtn, false);
                }
                selectedTeam.value = '0';
            } else if (team === 'circles') {
                createTeamBtn.classList.remove('hidden'); // Show Create Team button
                selectedTeam.value = '1';

                // Disable apply button if no team is created
                if (!is_team_created) {
                    setButtonDisabled(applyBtn, true);
                } else {
                    setButtonDisabled(btnUsers, true);
                    if (has_applied) {
                        createTeamBtn.classList.add('hidden');
                    } else {
                        createTeamBtn.textContent = '他にも呼ぶ';
                    }
                }
            }
        }

        window.onload = function() {
            if (has_pending_invitation) {
                toggleButtonStyles('circles');
                createTeamBtn.textContent = '他にも呼ぶ';
                setButtonDisabled(btnUsers, true);
                setButtonDisabled(applyBtn, true);
            }
            if (is_team_created) {
                toggleButtonStyles('circles');
                selectedTeam.value = '1';
            } else {
                toggleButtonStyles(selectedTeam.value === '0' ? 'users' : 'circles');
            }
        }
    </script>
<? return ob_get_clean();
}
