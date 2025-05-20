<h3>Survey Responses</h3>

<?php 
if (!empty($survey)): ?>
    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Question</th>
                <th>Answer</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($survey as $index => $item): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($item->question) ?></td>
                    <td><?= htmlspecialchars($item->option_text) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
endif;
    ?>
