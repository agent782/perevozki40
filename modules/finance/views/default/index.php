<?php

?>
<div class="container finance-default-index">
    <div class="row">
        <div class="col-lg-4">
            <label class="h4">Мониторинг</label>
            <?php if($count_outstanding_invoices):?>
            <p>Невыставленные счета - <?=$count_outstanding_invoices?></p>
            <?php endif;?>
            <?php if($count_outstanding_certificates):?>
            <p>Невыписанные акты - <?=$count_outstanding_certificates?></p>
            <?php endif;?>
            <?php if($count_request_payment):?>
                <p>Запросы на выплату - <?=$count_request_payment?></p>
            <?php endif;?>
            <p>Договора на проверку</p>

        </div>
        <div class="col-lg-8">
            <label class="h4">Информация о пользователе</label>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-4">
            <label class="h4">Статистика</label>
        </div>
        <div class="col-lg-4">
            <label class="h3">

            </label>
        </div>
        <div class="col-lg-4">
            <label class="h3">

            </label>
        </div>
    </div>
</div>


