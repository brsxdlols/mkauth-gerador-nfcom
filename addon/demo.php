<?php
include('addons.class.php');
mkauth_addon_require_login();
?>
<!DOCTYPE html>
<?php
if(isset($_SESSION['MM_Usuario'])){
    echo '<html lang="pt-BR">';
}else{
    echo '<html lang="pt-BR" class="has-navbar-fixed-top">';
}
?>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <title>MK - AUTH :: <?php echo $Manifest->{'name'}." - V ". $Manifest->{'version'};  ?></title>
        <link href="../../estilos/mk-auth.css" rel="stylesheet" type="text/css" />
        <link href="../../estilos/font-awesome.css" rel="stylesheet" type="text/css" />
        <link href="../../estilos/bi-icons.css" rel="stylesheet" type="text/css" />
        <script src="../../scripts/jquery.js"></script>
        <script src="../../scripts/mk-auth.js"></script>
        <link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="css/css.css" rel="stylesheet" type="text/css" />

</head>
<body>

<?php 
    include('config.hhvm'); 
    include('../../topo.php'); 
?>
<?php include 'nav/navbar.php'; ?>
	
<div class="container my-5">

 <div class="card shadow-sm mb-4">
  <div class="card-body">
    <h5 class="card-title">👨‍💼 PASSO 1 — Verificar homologação NFCom com seu contador</h5>
    <p>Como habilitar a NFCom (modelo 62)</p>

    <p>1️⃣ Entre em contato com seu contador<br>
    Peça para que ele habilite a emissão da NFCom (modelo 62) para o seu CNPJ junto à Receita Federal/SEFAZ da sua UF e confirme com você quando a habilitação estiver concluída.</p>

    <p>2️⃣ Aguarde a confirmação<br>
    Seu contador irá avisar quando tudo estiver pronto.</p>

    <p>3️⃣ Volte ao sistema MK-AUTH e continue a integração.</p>

    <p>OBS: Alguns estados, como Alagoas, já possuem habilitação automática para todos os CNPJs. Para os demais estados, verifique com seu contador se é necessário solicitar a habilitação.</p>
  </div>
</div>


<div class="card shadow-sm mb-4">
  <div class="card-body">
<h5 class="card-title">
  PASSO 2 — <i class="bi bi-diagram-3-fill"></i> Provedor &gt; <i class="bi bi-router"></i> Controle de Planos
</h5>


  <form onsubmit="return false;">
    <div class="mb-2" style="font-size: 14px;">
      <label class="form-label">Tipo</label><br>
      <input type="radio" disabled> dedicado [assinatura]  
      <input type="radio" disabled checked> semi-dedicado
    </div>

    <div class="mb-2" style="font-size: 14px;">
      <label class="form-label">Serviço</label>
      <select class="form-select form-select-sm" disabled>
        <option selected>4 - Provimento de Acesso à Internet</option>
      </select>
    </div>

    <div class="mb-2" style="font-size: 14px;">
      <label class="form-label">CFOP</label>
      <select class="form-select form-select-sm" disabled>
        <option selected>5307 - Prestação de serviço de comunicação a não-contribuinte</option>
      </select>
    </div>

    <div class="mb-2" style="font-size: 14px;">
      <label class="form-label">Alíquota % ICMS</label>
      <input type="text" class="form-control form-control-sm" value="0,00" disabled>
    </div>

    <div class="mb-2" style="font-size: 14px;">
      <label class="form-label">ICMS CST</label>
      <select class="form-select form-select-sm" disabled>
        <option selected>00 - Tributação normal ICMS</option>
      </select>
    </div>

    <div class="mb-2" style="font-size: 14px;">
      <label class="form-label">Alíquota % PIS</label>
      <input type="text" class="form-control form-control-sm" value="0,00" disabled>
    </div>

    <div class="mb-2" style="font-size: 14px;">
      <label class="form-label">PIS CST</label>
      <select class="form-select form-select-sm" disabled>
        <option selected>01 - Tributável com alíquota básica</option>
      </select>
    </div>

    <div class="mb-2" style="font-size: 14px;">
      <label class="form-label">Alíquota % COFINS</label>
      <input type="text" class="form-control form-control-sm" value="0,00" disabled>
    </div>

    <div class="mb-2" style="font-size: 14px;">
      <label class="form-label">COFINS CST</label>
      <select class="form-select form-select-sm" disabled>
        <option selected>01 - Operação Tributável com alíquota básica</option>
      </select>
    </div>

    <div class="mb-2" style="font-size: 14px;">
      <label class="form-label">Alíquota % FUST</label>
      <input type="text" class="form-control form-control-sm" value="0,00" disabled>
    </div>

    <div class="mb-2" style="font-size: 14px;">
      <label class="form-label">Alíquota % FUNTTEL</label>
      <input type="text" class="form-control form-control-sm" value="0,00" disabled>
    </div>

    <div class="mb-2" style="font-size: 14px;">
      <label class="form-label">Alíquota % CSLL</label>
      <input type="text" class="form-control form-control-sm" value="0,00" disabled>
    </div>

    <div class="mb-2" style="font-size: 14px;">
      <label class="form-label">Alíquota % IRRF</label>
      <input type="text" class="form-control form-control-sm" value="0,00" disabled>
    </div>

    <div class="mb-2" style="font-size: 14px;">
      <label class="form-label">Alíquota % IBSCBS</label>
      <input type="text" class="form-control form-control-sm" value="0,00" disabled>
    </div>
   </form>
  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-body">
    <h5 class="card-title">🧩 PASSO 3 — Atualizar e Configurar Certificado Digital</h5>
    <p>1️⃣ Atualize o sistema para a versão 25.08.</p>
    <p>2️⃣ Acesse o painel administrativo do MK-AUTH.</p>
    <p>3️⃣ No menu lateral, vá em <i class="bi bi-diagram-3-fill"></i> <strong>Provedor</strong> → <i class="bi bi-journal-bookmark"></i> <strong>Dados do Provedor</strong>.</p>
    <p>4️⃣ Clique na aba <i class="fa fa-university"></i> <strong>Fiscal</strong>.</p>
    <p>5️⃣ Confira se o CNPJ e a Inscrição Estadual estão preenchidos apenas com os números, sem pontos e sem traços.</p>
    <p>6️⃣Localize o campo para enviar o Certificado Digital A1 (.pfx).</p>
    <p>7️⃣ Anexe o arquivo do certificado A1 do seu CNPJ e informe a senha correspondente.</p>
    <p>8️⃣ Verifique se o certificado A1 está válido e dentro do prazo de validade.</p> 
<div style="display: flex; align-items: center; gap: 8px;">
  <span>9️⃣ Clique em</span>
  <span>Enviar novo:</span>
  <button style="background-color: rgb(50, 115, 220); color: white; border: none; padding: 6px 18px; border-radius: 5px 0 0 5px; font-weight: 600; cursor: default; display: inline-flex; align-items: center; gap: 6px;">
    <i class="bi bi-upload" style="font-size: 16px;"></i>
    Enviar arquivo
  </button>
  <input type="text" value="Certificado e-CNPJ" readonly
    style="border: 1px solid #ccc; border-left: none; background-color: white; padding: 6px 12px; border-radius: 0 5px 5px 0; font-weight: 500; width: 220px; cursor: default;">
</div>   

 <p>🔟 Após alterações, clique em 
  <button style="background-color: rgb(50, 115, 220); color: white; border: none; padding: 6px 18px; border-radius: 5px; font-weight: 600; cursor: default;">
    Gravar
  </button>
</p>

  
    <div style="background-color: #fff3cd; border-left: 5px solid #ffc107; padding: 12px 16px; border-radius: 6px; margin-top: 10px;">
      <i class="bi bi-exclamation-triangle-fill" style="color: #856404; margin-right: 6px;"></i>
      <strong>Atenção:</strong> Caso apareça erro de comunicação, confirme se o servidor da prefeitura está disponível no momento.
    </div>
  </div>




<div class="card shadow-sm mb-4">
  <div class="card-body">
    <h5 class="card-title">🔗 PASSO 4 — Cadastro da Serie (NFCom)</h5>
    <p>1️⃣ Acesse o menu Financeiro.</p>
    <p>2️⃣ Vá até a aba <i class="bi bi-receipt"></i> <strong>NFCom Geradas</strong>.</p>
    <p>3️⃣ No canto inferior, clique na engrenagem <i class="bi bi-gear-fill" style="font-size: 1.5em; color: rgb(50, 115, 220) !important;"></i> para abrir as configurações.</p>
    <p>4️⃣ Configure os campos conforme abaixo:</p>

 
<div class="border rounded p-2 mb-3" style="font-weight:bold; color: #856404; text-align: left;">
  ⚠️ NOTAS NÃO FISCAIS — APENAS PARA TESTE<br>
   <span style="font-size: 1.0em;">Ambiente: Homologação</span><br>
  <span style="font-size: 1.0em;">Layout: modelo01</span><br>
  <span style="font-size: 1.0em;">Série: 999</span>
</div>


<div class="bg-light border rounded p-3 mb-3 text-success fw-bold">
  <p>✅ ATENÇÃO: PRODUÇÃO JÁ É Enviado para SEFAZ</p>
  <p>Ambiente: Produção</p>
  <p>Layout: modelo01</p>
  <p>Série: 1 (clientes residenciais)</p>
  <p>Série: 2 (clientes corporativos)</p>
  <p>Número inicial: 1</p>
</div>


    <p>5️⃣ Clique em 
      <button style="background-color: rgb(50, 115, 220); color: white; border: none; padding: 6px 18px; border-radius: 5px; font-weight: 600; cursor: default;">
          Gravar
      </button>  
    </p>

  </div>
</div>


  <!-- PASSO 3 -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="card-title">🧾 PASSO 5 — Emitindo NFCom</h5>
      <p>1️⃣ Vá até o menu Financeiro → Títulos.</p>
      <p>2️⃣ Selecione os títulos já pagos que deseja emitir a NFCom.</p>
      <p>3️⃣ Role até o final da página.</p>
      <p>4️⃣ Clique no ícone “ bi bi-receipt Processar NFCom” .</p>
      <p>5️⃣ O sistema irá listar os clientes/recebimentos marcados — confira as informações.</p>
<p>6️⃣ Após conferir os usuários selecionados, clique em 
 <button style="background-color: rgb(50, 115, 220); color: white; border: none; padding: 6px 18px; border-radius: 5px; font-weight: 600; cursor: default;">
  <i class="bi bi-receipt" style="margin-right: 5px; color: #00d1b2;"></i> Processar para NFCom
</button>

</p>


    </div>
  </div>

</div>


    <div class="pt-3">
        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap" style="font-size: 12px; padding: 8px 0;">
            
            <span><strong>Addon NFCOM NOTA 62
            <a href="https://wa.me/5582987361943?text=Ol%C3%A1%2C+gostaria+de+saber+mais+sobre+o+addon+de+Envio+de+Mensagens+Personalizadas" 
               class="btn btn-link p-0 m-0 text-success" style="font-size: 11px;" target="_blank">
            </a>
        </div>
    </div>

<?php include('../../baixo.php'); ?>
<script src="../../menu.js.hhvm"></script>

</body>
</html>
