<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>



<div class="d-flex justify-content-center align-items-center" style="height: 50px;">
    <ul class="list-unstyled mb-0">
        <li style="color: blue; font-size: 18px;"><?php echo $Manifest->{'name'} . " V " . $Manifest->{'version'}; ?></li>
    </ul>
</div>

    </ul>
</nav>


<style>
.bullet {
    font-size: 12px;
    color: #999;
    margin-left: 6px;
}
</style>


<div class="menu-horizontal">
            <ul>
                <li>
            <a href="index.hhvm" class="<?= ($current_page == 'index.hhvm') ? 'active' : '' ?>">
            Gerar 1º Etapa
        </a>

        <li>
            <a href="processar_2.hhvm" class="<?= ($current_page == 'processar_2.hhvm') ? 'active' : '' ?>">
                Validar 2º Etapa
            </a>
              </li>
        <li>
            <a href="nfcom_concluidas.hhvm" class="<?= ($current_page == 'nfcom_concluidas.hhvm') ? 'active' : '' ?>">
                <i class="bi bi-receipt" style="color: #00ff22ff;"></i> NFCom Cocluidas
            </a>
        </li>
                    <li>
            <a href="nfcom_pagina.hhvm" class="<?= ($current_page == 'nfcom_pagina.hhvm') ? 'active' : '' ?>">
                <i class="bi bi-file-excel" style="color: #ff0606ff;"></i>Rejeitadas/Cancelada
            </a>
        </li>
        <li>
            <a href="dici_mensal.php" class="<?= ($current_page == 'dici_mensal.php') ? 'active' : '' ?>">
                <i class="bi bi-receipt-cutoff" style="color: #0d6efd;"></i> Dici Mensal
            </a>
         </li>
        <li>
            <a href="trimestral.php" class="<?= ($current_page == 'trimestral.php') ? 'active' : '' ?>">
                <i class="bi bi-receipt-cutoff" style="color: #25d366;"></i> PPP Trimestral
            </a>
        </li>
        <li>
            <a href="ativarnfdici.php" class="<?= ($current_page == 'ativarnfdici.php') ? 'active' : '' ?>">
                <i class="bi bi-journal-richtext" style="color: #25d366;"></i> Ativar Dici/NF
            </a>
        </li>        
        <li>
      <a href="demo.php" class="<?= ($current_page == 'demo.php') ? 'active' : '' ?>">
    <i style="color: #100324ff;"></i> 📘 (Guia NFCOM)
</a>
        </li>
    </ul>
</div>


<style>
.menu-horizontal {
    text-align: center;
    padding: 10px 0;
}
.menu-horizontal ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: inline-flex;
    gap: 2px;
    flex-wrap: wrap;
    justify-content: center;
}
.menu-horizontal li a {
    text-decoration: none;
    font-size: 16px;
    color: #333;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 6px;
    transition: background 0.2s;
}
.menu-horizontal li a.active {
    background-color: #e9ecef;
    font-weight: bold;
}
.menu-horizontal li a:hover {
    background-color: #f8f9fa;
}
</style>
