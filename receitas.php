<?php
include 'db.php';

// Buscar lista de clientes para o select
$sql_clientes = "SELECT cliente_id, nome_cliente FROM clientes ORDER BY nome_cliente";
$result_clientes = mysqli_query($conn, $sql_clientes);

// Processar upload do arquivo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paciente_id = $_POST['paciente'] ?? '';
    $medico = $_POST['medico'] ?? '';
    $data_receita = $_POST['data_receita'] ?? '';
    $observacoes = $_POST['observacoes'] ?? '';

    // Buscar nome do cliente selecionado
    $nome_paciente = '';
    if (!empty($paciente_id)) {
        $sql_nome_cliente = "SELECT nome_cliente FROM clientes WHERE cliente_id = ?";
        $stmt_nome = $conn->prepare($sql_nome_cliente);
        $stmt_nome->bind_param("i", $paciente_id);
        $stmt_nome->execute();
        $result_nome = $stmt_nome->get_result();
        $cliente = $result_nome->fetch_assoc();
        $nome_paciente = $cliente['nome_cliente'] ?? '';
    }

    // Verificar se um arquivo foi enviado
    if (isset($_FILES['arquivo_receita']) && $_FILES['arquivo_receita']['error'] === UPLOAD_ERR_OK) {
        // Informações do arquivo
        $file_name = $_FILES['arquivo_receita']['name'];
        $file_tmp = $_FILES['arquivo_receita']['tmp_name'];
        $file_size = $_FILES['arquivo_receita']['size'];
        $file_type = $_FILES['arquivo_receita']['type'];
        
        // Verificar se é um PDF
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['pdf'];
        
        if (in_array($file_ext, $allowed_ext)) {
            // Limitar tamanho do arquivo (5MB)
            if ($file_size <= 5 * 1024 * 1024) {
                // Diretório de upload
                $upload_dir = 'receitas/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Gerar nome único para o arquivo
                $new_file_name = uniqid('receita_', true) . '.' . $file_ext;
                $upload_path = $upload_dir . $new_file_name;
                
                // Mover arquivo para o diretório
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    // Inserir no banco de dados
                    $sql = "INSERT INTO receitas (cliente_id, paciente, medico, data_receita, arquivo_path, observacoes, data_cadastro) 
                            VALUES (?, ?, ?, ?, ?, ?, NOW())";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("isssss", $paciente_id, $nome_paciente, $medico, $data_receita, $upload_path, $observacoes);
                    
                    if ($stmt->execute()) {
                        $success_msg = "Receita cadastrada com sucesso!";
                        // Limpar formulário
                        $_POST = array();
                    } else {
                        $error_msg = "Erro ao salvar no banco de dados: " . $conn->error;
                    }
                } else {
                    $error_msg = "Erro ao fazer upload do arquivo.";
                }
            } else {
                $error_msg = "O arquivo é muito grande. Tamanho máximo permitido: 5MB.";
            }
        } else {
            $error_msg = "Apenas arquivos PDF são permitidos.";
        }
    } else {
        $error_msg = "Nenhum arquivo foi enviado ou ocorreu um erro no upload.";
    }
}

// Buscar receitas cadastradas
$sql_receitas = "SELECT r.*, c.cpf_cliente, c.celular_cliente 
                 FROM receitas r
                 LEFT JOIN clientes c ON r.cliente_id = c.cliente_id
                 ORDER BY r.data_receita DESC";
$result_receitas = mysqli_query($conn, $sql_receitas);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Farmácia - Gerenciamento de Receitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --light-color: #f8f9fa;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .card-receita {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s;
            margin-bottom: 20px;
        }
        
        .card-receita:hover {
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .btn-receita {
            background-color: var(--primary-color);
            color: white;
            border-radius: 8px;
            padding: 8px 15px;
            font-weight: 500;
        }
        
        .btn-receita:hover {
            background-color: #0b5ed7;
            color: white;
        }
        
        .section-title {
            border-left: 5px solid var(--primary-color);
            padding-left: 10px;
            margin: 30px 0 20px;
        }
        
        .file-upload-container {
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            background-color: #f9f9f9;
            margin-bottom: 20px;
        }
        
        .file-upload-container:hover {
            border-color: var(--primary-color);
        }
        
        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 5px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <h2 class="section-title">Gerenciamento de Receitas Médicas</h2>
            
            <?php if (isset($success_msg)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_msg)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-file-earmark-plus"></i> Cadastrar Nova Receita</h5>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="paciente" class="form-label">Paciente</label>
                            <select class="form-select select2" id="paciente" name="paciente" required>
                                <option value="">Selecione um cliente...</option>
                                <?php 
                                mysqli_data_seek($result_clientes, 0); // Resetar ponteiro do resultado
                                while ($cliente = mysqli_fetch_assoc($result_clientes)): ?>
                                    <option value="<?php echo $cliente['cliente_id']; ?>" <?php echo (isset($_POST['paciente']) && $_POST['paciente'] == $cliente['cliente_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cliente['nome_cliente']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="medico" class="form-label">Médico</label>
                                <input type="text" class="form-control" id="medico" name="medico" value="<?php echo isset($_POST['medico']) ? htmlspecialchars($_POST['medico']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="data_receita" class="form-label">Data da Receita</label>
                                <input type="date" class="form-control" id="data_receita" name="data_receita" value="<?php echo isset($_POST['data_receita']) ? htmlspecialchars($_POST['data_receita']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea class="form-control" id="observacoes" name="observacoes" rows="2"><?php echo isset($_POST['observacoes']) ? htmlspecialchars($_POST['observacoes']) : ''; ?></textarea>
                        </div>
                        
                        <div class="file-upload-container mb-3">
                            <i class="bi bi-file-earmark-pdf" style="font-size: 2rem; color: var(--danger-color);"></i>
                            <h5>Arraste e solte o arquivo PDF aqui ou clique para selecionar</h5>
                            <p class="text-muted">Apenas arquivos PDF são aceitos (Tamanho máximo: 5MB)</p>
                            <input type="file" class="form-control d-none" id="arquivo_receita" name="arquivo_receita" accept=".pdf" required>
                            <label for="arquivo_receita" class="btn btn-receita mt-2">
                                <i class="bi bi-upload"></i> Selecionar Arquivo
                            </label>
                            <div id="file-name" class="mt-2 text-muted"></div>
                        </div>
                        
                        <button type="submit" class="btn btn-receita w-100">
                            <i class="bi bi-save"></i> Salvar Receita
                        </button>
                    </form>
                </div>
            </div>
            
            <h3 class="section-title">Receitas Cadastradas</h3>
            
            <?php if (mysqli_num_rows($result_receitas) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Paciente</th>
                                <th>CPF</th>
                                <th>Contato</th>
                                <th>Médico</th>
                                <th>Data Receita</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($receita = mysqli_fetch_assoc($result_receitas)): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($receita['paciente']); ?></strong>
                                        <?php if (!empty($receita['observacoes'])): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($receita['observacoes']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo !empty($receita['cpf_cliente']) ? htmlspecialchars($receita['cpf_cliente']) : '--'; ?></td>
                                    <td><?php echo !empty($receita['celular_cliente']) ? htmlspecialchars($receita['celular_cliente']) : '--'; ?></td>
                                    <td><?php echo htmlspecialchars($receita['medico']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($receita['data_receita'])); ?></td>
                                    <td>
                                        <a href="<?php echo $receita['arquivo_path']; ?>" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-danger"
                                           title="Visualizar PDF">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                        <a href="#" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    Nenhuma receita cadastrada ainda.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Inicializar Select2
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Selecione um cliente...",
            allowClear: true,
            width: '100%'
        });
        
        // Mostrar nome do arquivo selecionado
        $('#arquivo_receita').change(function(e) {
            const fileName = e.target.files[0]?.name || 'Nenhum arquivo selecionado';
            $('#file-name').text(fileName);
        });
        
        // Efeito de drag and drop
        const uploadContainer = document.querySelector('.file-upload-container');
        const fileInput = document.getElementById('arquivo_receita');
        
        uploadContainer.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadContainer.style.borderColor = 'var(--primary-color)';
            uploadContainer.style.backgroundColor = '#f0f7ff';
        });
        
        uploadContainer.addEventListener('dragleave', () => {
            uploadContainer.style.borderColor = '#ddd';
            uploadContainer.style.backgroundColor = '#f9f9f9';
        });
        
        uploadContainer.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadContainer.style.borderColor = '#ddd';
            uploadContainer.style.backgroundColor = '#f9f9f9';
            
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                $('#file-name').text(e.dataTransfer.files[0].name);
            }
        });
    });
</script>

</body>
</html>