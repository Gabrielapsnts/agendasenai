<?php
include "conexao_db.php";

// ----------------- Buscar professores (agrupando por nome para evitar duplicados) -----------------
$sqlAllProf = "SELECT id_prof, nomeprof FROM professor ORDER BY nomeprof ASC";
$resAllProf = $conn->query($sqlAllProf);

$idsByName = [];
$profNameById = [];

if ($resAllProf) {
  while ($r = $resAllProf->fetch_assoc()) {
    $id = $r['id_prof'];
    $name = trim($r['nomeprof']);
    $profNameById[$id] = $name;
    if (!isset($idsByName[$name])) $idsByName[$name] = [];
    $idsByName[$name][] = $id;
  }
}

// Criar lista de professores únicos (uma opção por nome)
$professores = [];
foreach ($idsByName as $name => $ids) {
  $professores[] = [
    'id_prof' => intval($ids[0]),
    'nomeprof' => $name,
    'all_ids' => $ids
  ];
}

// ----------------- Buscar cursos e competências por professor -----------------
$sqlCursos = "
  SELECT p.id_prof, uc.iduc, uc.nomeuc, c.idcomp, c.nomecomp
  FROM professor p
  JOIN professor_uc puc ON p.id_prof = puc.id_prof
  JOIN uc ON puc.iduc = uc.iduc
  JOIN uc_comp ucc ON uc.iduc = ucc.iduc
  JOIN competencia c ON ucc.idcomp = c.idcomp
  ORDER BY p.id_prof, uc.nomeuc, c.nomecomp
";

$resCursos = $conn->query($sqlCursos);
$cursosPorProfessorById = [];

if ($resCursos) {
  while ($row = $resCursos->fetch_assoc()) {
    $idProf = $row['id_prof'];
    $idUc = $row['iduc'];
    if (!isset($cursosPorProfessorById[$idProf])) $cursosPorProfessorById[$idProf] = [];
    if (!isset($cursosPorProfessorById[$idProf][$idUc])) {
      $cursosPorProfessorById[$idProf][$idUc] = [
        'nomeuc' => $row['nomeuc'],
        'competencias' => []
      ];
    }

    $exists = false;
    foreach ($cursosPorProfessorById[$idProf][$idUc]['competencias'] as $c) {
      if ($c['idcomp'] == $row['idcomp']) {
        $exists = true;
        break;
      }
    }

    if (!$exists) {
      $cursosPorProfessorById[$idProf][$idUc]['competencias'][] = [
        'idcomp' => $row['idcomp'],
        'nomecomp' => $row['nomecomp']
      ];
    }
  }
}

// ----------------- Agregar cursos por professor único -----------------
$cursosPorProfessor = [];
foreach ($professores as $prof) {
  $primary = $prof['id_prof'];
  $allIds = $prof['all_ids'];
  $merged = [];

  foreach ($allIds as $pid) {
    if (!isset($cursosPorProfessorById[$pid])) continue;
    foreach ($cursosPorProfessorById[$pid] as $idUc => $ucData) {
      if (!isset($merged[$idUc])) {
        $merged[$idUc] = [
          'nomeuc' => $ucData['nomeuc'],
          'competencias' => []
        ];
      }
      foreach ($ucData['competencias'] as $comp) {
        $found = false;
        foreach ($merged[$idUc]['competencias'] as $existing) {
          if ($existing['idcomp'] == $comp['idcomp']) {
            $found = true;
            break;
          }
        }
        if (!$found) $merged[$idUc]['competencias'][] = $comp;
      }
    }
  }
  $cursosPorProfessor[$primary] = $merged;
}

// ----------------- Verificar professor selecionado -----------------
$selectedProf = isset($_GET['id_prof']) ? intval($_GET['id_prof']) : null;

// ----------------- Buscar agendamentos (eventos) -----------------
$eventos = [];
if ($selectedProf) {
  $sqlEv = "
    SELECT a.id, a.id_prof, a.id_uc, a.id_comp, a.data_inicio, a.data_fim,
           uc.nomeuc, c.nomecomp
    FROM agenda a
    JOIN uc ON a.id_uc = uc.iduc
    JOIN competencia c ON a.id_comp = c.idcomp
    WHERE a.id_prof = ?
  ";
  $stmt = $conn->prepare($sqlEv);
  if ($stmt) {
    $stmt->bind_param("i", $selectedProf);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
      $eventos[] = [
        'id' => $r['id'],
        'id_prof' => $r['id_prof'],
        'id_uc' => $r['id_uc'],
        'id_comp' => $r['id_comp'],
        'data_inicio' => $r['data_inicio'],
        'data_fim' => $r['data_fim'],
        'nomeuc' => $r['nomeuc'],
        'nomecomp' => $r['nomecomp']
      ];
    }
    $stmt->close();
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Agenda - SENAI</title>
  <link rel="stylesheet" href="../bootstrap/bootstrap.css">
  <link rel="stylesheet" href="../css/tabela_agenda.css">
</head>
<body>
  
<nav class="navbar navbar-expand-lg" style="background-color: #0a0d8d; margin-top: 0px;">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <a class="navbar-brand" style="color:white" href="dashboard.php">AGENDA SENAI</a>
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link active" style="color:white" href="tabela_uc.php">Cursos</a></li>
      <li class="nav-item"><a class="nav-link active" style="color:white" href="tabela_prof.php">Professores</a></li>
      <li class="nav-item"><a class="nav-link active" style="color:white" href="logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="container">
  <div style="display:flex; gap:24px; align-items:flex-start;">
    
    <!-- Calendário -->
    <div style="flex:1; min-width: 800px;">
      <div class="controls" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; width: 100%;">
        <button onclick="changeMonth(-1)" class="btn btn-primary" style="width: 150px;">← Mês Anterior</button>
        <h4 id="monthYear" style="margin:0; width: 200px; margin-left: -170px;"></h4>
        <button onclick="changeMonth(1)" class="btn btn-primary" style="width: 150px;">Próximo Mês →</button>
      </div>

      <table class="calendar" style="width: 100%;">
        <thead>
          <tr>
            <th>Dom</th><th>Seg</th><th>Ter</th><th>Qua</th><th>Qui</th><th>Sex</th><th>Sáb</th>
          </tr>
        </thead>
        <tbody id="calendarBody"></tbody>
      </table>
    </div>

    <!-- Lateral direita -->
    <div style="width:360px; margin-left: 40px; margin-top: -50px" id="agendaFormWrapper">
      <div class="prof-card" style="margin-bottom:12px;">
        <label style="font-weight:700; display:block; margin-bottom:6px;">Professor</label>
        <div style="display:flex; gap:8px; align-items:center;">
          <select id="main_prof_select" class="form-select" style="flex:1;">
            <option value="">Selecione o professor</option>
            <?php foreach ($professores as $p): ?>
              <option value="<?= $p['id_prof'] ?>" <?= ($selectedProf && $selectedProf == $p['id_prof']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($p['nomeprof']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button id="btnEdit" class="btn btn-outline-primary">Editar</button>
        </div>
        <small style="display:block; margin-top:6px; color:#666;">Selecione um professor para ver a agenda. Clique em Editar para abrir o formulário.</small>
      </div>

      <!-- Formulário -->
      <form action="salvar_agenda.php" method="POST" id="formSalvar" style="display:none;">
        <input type="hidden" name="id_prof" id="form_id_prof" value="<?= $selectedProf ? $selectedProf : '' ?>">

        <div class="form-group">
          <label>Unidade Curricular (UC):</label>
          <select name="id_uc" id="form_id_uc" class="form-select" required>
            <option value="">-- selecione a UC --</option>
          </select>
        </div>

        <div class="form-group">
          <label>Competência:</label>
          <select name="id_comp" id="form_id_comp" class="form-select" required>
            <option value="">-- selecione a competência --</option>
          </select>
        </div>

        <div class="form-group">
          <label>Data Início:</label>
          <input type="date" name="data_inicio" class="form-control" required>
        </div>

        <div class="form-group">
          <label>Data Fim:</label>
          <input type="date" name="data_fim" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Salvar</button>
      </form>
    </div>
  </div>
</div>

<script>
  const cursosPorProfessor = <?php echo json_encode($cursosPorProfessor); ?>;
  const eventos = <?php echo json_encode($eventos); ?>;
  const selectedProf = <?= $selectedProf ? $selectedProf : 'null' ?>;

  // --- JS igual, sem alterações ---
  const mainProfSelect = document.getElementById('main_prof_select');
  const btnEdit = document.getElementById('btnEdit');
  const formHiddenProf = document.getElementById('form_id_prof');
  const formUc = document.getElementById('form_id_uc');
  const formComp = document.getElementById('form_id_comp');
  const formSalvar = document.getElementById('formSalvar');
  const monthYear = document.getElementById('monthYear');
  const calendarBody = document.getElementById('calendarBody');
  let currentDate = new Date();

  mainProfSelect.addEventListener('change', () => {
    const val = mainProfSelect.value;
    window.location.href = val ? `tabela_agenda.php?id_prof=${val}` : 'tabela_agenda.php';
  });

  btnEdit.addEventListener('click', e => {
    e.preventDefault();
    const val = mainProfSelect.value;
    if (!val) return alert('Selecione um professor antes de editar.');
    formHiddenProf.value = val;
    popularUCs(val);
    formSalvar.style.display = 'block';
    formSalvar.scrollIntoView({ behavior: 'smooth', block: 'center' });
  });

  function popularUCs(profId) {
    formUc.innerHTML = '<option value="">-- selecione a UC --</option>';
    formComp.innerHTML = '<option value="">-- selecione a competência --</option>';
    if (!cursosPorProfessor[profId]) return;
    for (const idUc in cursosPorProfessor[profId]) {
      const opt = document.createElement('option');
      opt.value = idUc;
      opt.textContent = cursosPorProfessor[profId][idUc].nomeuc;
      formUc.appendChild(opt);
    }
  }

  function popularCompetencias(profId, ucId) {
    formComp.innerHTML = '<option value="">-- selecione a competência --</option>';
    if (!profId || !ucId) return;
    const comps = cursosPorProfessor[profId][ucId].competencias || [];
    comps.forEach(c => {
      const opt = document.createElement('option');
      opt.value = c.idcomp;
      opt.textContent = c.nomecomp;
      formComp.appendChild(opt);
    });
  }

  formUc.addEventListener('change', function() {
    popularCompetencias(formHiddenProf.value, this.value);
  });

  window.addEventListener('load', function() {
    if (selectedProf) {
      mainProfSelect.value = selectedProf;
      formHiddenProf.value = selectedProf;
      popularUCs(selectedProf);
    }
    renderCalendar();
  });

  function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const today = new Date();
    const monthNames = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
    monthYear.textContent = `${monthNames[month]} ${year}`;
    calendarBody.innerHTML = '';
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    let date = 1;

    for (let i = 0; i < 6; i++) {
      const tr = document.createElement('tr');
      for (let j = 0; j < 7; j++) {
        const td = document.createElement('td');
        if (i === 0 && j < firstDay) {
        } else if (date > daysInMonth) {
        } else {
          const divNum = document.createElement('div');
          divNum.className = 'day-number';
          divNum.textContent = date;
          td.appendChild(divNum);

          const thisDateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
          const dayOfWeek = new Date(year, month, date).getDay();

          if (dayOfWeek !== 0) {
            for (let ev of eventos) {
              if (ev.data_inicio <= thisDateStr && thisDateStr <= ev.data_fim) {
                const evDiv = document.createElement('div');
                evDiv.className = 'event';
                evDiv.innerHTML = `<b>${ev.nomeuc}</b><br>${ev.nomecomp}`;
                td.appendChild(evDiv);
              }
            }
          }

          if (date === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
            td.classList.add('today');
          }

          date++;
        }
        tr.appendChild(td);
      }
      calendarBody.appendChild(tr);
    }
  }

  function changeMonth(offset) {
    currentDate.setMonth(currentDate.getMonth() + offset);
    renderCalendar();
  }


</script>
</body>
</html>
