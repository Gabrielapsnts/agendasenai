<?php
include "conexao_db.php";


// Buscar professores cadastrados
$sqlProfessores = "SELECT id_prof, nomeprof FROM professor ORDER BY nomeprof ASC";
$resultProf = $conn->query($sqlProfessores);

$professores = [];
if ($resultProf) {
    while ($row = $resultProf->fetch_assoc()) {
        $professores[] = $row;
    }
}

// Buscar cursos e competências por professor
$sqlCursos = "
    SELECT p.id_prof, uc.iduc, uc.nomeuc, c.idcomp, c.nomecomp
    FROM professor p
    JOIN professor_uc puc ON p.id_prof = puc.id_prof
    JOIN uc ON puc.iduc = uc.iduc
    JOIN uc_comp ucc ON uc.iduc = ucc.iduc
    JOIN competencia c ON ucc.idcomp = c.idcomp
    ORDER BY p.id_prof, uc.nomeuc, c.nomecomp
";
$resultCursos = $conn->query($sqlCursos);

$cursosPorProfessor = [];
if ($resultCursos) {
    while ($row = $resultCursos->fetch_assoc()) {
        $idProf = $row['id_prof'];
        $idUc = $row['iduc'];

        if (!isset($cursosPorProfessor[$idProf])) {
            $cursosPorProfessor[$idProf] = [];
        }
        if (!isset($cursosPorProfessor[$idProf][$idUc])) {
            $cursosPorProfessor[$idProf][$idUc] = [
                'nomeuc' => $row['nomeuc'],
                'competencias' => []
            ];
        }
        $cursosPorProfessor[$idProf][$idUc]['competencias'][] = [
            'idcomp' => $row['idcomp'],
            'nomecomp' => $row['nomecomp']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Calendário Mensal em Grade</title>
  <link rel="stylesheet" href="../bootstrap/bootstrap.css">
  <link rel="stylesheet" href="../css/tabela_agenda.css">
  
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div style="background-color: #0a0d8d;" class="container-fluid d-flex justify-content-between align-items-center">
      <a class="navbar-brand" style="color:white" href="dashboard.php">AGENDA SENAI</a>
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" style="color:white" href="tabela_uc.php">Cursos</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" style="color:white" href="tabela_prof.php">Professores</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" style="color:white" href="logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </nav>

  <div class="calendar-wrapper" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; padding: 20px;">
    <!-- Calendário -->
    <div class="calendar-container" style="flex-grow: 1;">
      <div class="calendar-header">
        <button onclick="changeMonth(-1)">← Mês Anterior</button>
        <h1 id="monthYear"></h1>
        <button onclick="changeMonth(1)">Próximo Mês →</button>
      </div>
      <table class="calendar">
        <thead>
          <tr>
            <th>Dom</th>
            <th>Seg</th>
            <th>Ter</th>
            <th>Qua</th>
            <th>Qui</th>
            <th>Sex</th>
            <th>Sáb</th>
          </tr>
        </thead>
        <tbody id="calendarBody"></tbody>
      </table>
    </div>

    <!-- Seleção de Professor + Formulário -->
    <div class="select-container" style="min-width: 300px;">
      <form action="salvar_agenda.php" method="POST">
        <label for="nomeprof">Professor:</label>
        <select id="nomeprof" class="form-select" name="id_prof" required>
          <option value="" disabled selected hidden>Selecione o professor</option>
          <?php foreach ($professores as $prof): ?>
            <option value="<?= htmlspecialchars($prof['id_prof']) ?>">
              <?= htmlspecialchars($prof['nomeprof']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <div class="mb-3" style="margin-top: 15px;">
          <label for="emailprof" class="form-label">Enviar para</label>
          <input type="email" class="form-control" id="emailprof" name="emailprof" required>
          <div id="emailHelp" class="form-text">Insira o email corporativo do(a) professor(a)</div>
        </div>

        <button type="submit" class="btn btn-primary">Enviar</button>
        <br><br>
        <button type="button" id="btnEditar" class="btn btn-outline-primary" onclick="mostrarFormulario()" disabled>Editar calendário</button>

        <div id="form-edicao" style="display: none; margin-top: 20px;">
          <label for="curso">Curso:</label>
          <select id="curso" class="form-select" onchange="carregarCompetencias()">
            <option value="" selected disabled hidden>Selecione um curso</option>
          </select>

          <br>
          <label for="competencia">Competência:</label>
          <select id="competencia" class="form-select">
            <option value="" selected disabled hidden>Selecione uma competência</option>
          </select>

          <br>
          <label for="data_inicio">Data de Início:</label>
          <input type="date" class="form-control" id="data_inicio" />

          <br>
          <label for="data_fim">Data de Fim:</label>
          <input type="date" class="form-control" id="data_fim" />

          <br>
          <button type="button" class="btn btn-outline-primary" onclick="salvarAgenda()">Salvar</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Código do calendário
    const monthYear = document.getElementById('monthYear');
    const calendarBody = document.getElementById('calendarBody');
    let currentDate = new Date();

    function renderCalendar() {
      const year = currentDate.getFullYear();
      const month = currentDate.getMonth();
      const today = new Date();

      const monthNames = [
        'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
      ];

      monthYear.textContent = `${monthNames[month]} ${year}`;
      calendarBody.innerHTML = '';

      const firstDay = new Date(year, month, 1).getDay();
      const daysInMonth = new Date(year, month + 1, 0).getDate();

      let date = 1;
      for (let i = 0; i < 6; i++) {
        const row = document.createElement('tr');

        for (let j = 0; j < 7; j++) {
          const cell = document.createElement('td');

          if (i === 0 && j < firstDay) {
            cell.innerHTML = '';
          } else if (date > daysInMonth) {
            break;
          } else {
            const isToday = date === today.getDate() &&
                            month === today.getMonth() &&
                            year === today.getFullYear();

            if (isToday) cell.classList.add('today');

            const dayDiv = document.createElement('div');
            dayDiv.classList.add('day-number');
            dayDiv.textContent = date;
            cell.appendChild(dayDiv);

            date++;
          }
          row.appendChild(cell);
        }
        calendarBody.appendChild(row);
        if (date > daysInMonth) break;
      }
    }

    function changeMonth(offset) {
      currentDate.setMonth(currentDate.getMonth() + offset);
      renderCalendar();
    }

    renderCalendar();
  </script>

  <script>
    const cursosPorProfessor = <?php echo json_encode($cursosPorProfessor); ?>;

    const profSelect = document.getElementById('nomeprof');
    const btnEditar = document.getElementById('btnEditar');

    profSelect.addEventListener('change', () => {
      btnEditar.disabled = false;
    });

    function mostrarFormulario() {
      const formEdicao = document.getElementById('form-edicao');
      formEdicao.style.display = 'block';

      const profId = document.getElementById('nomeprof').value;
      const cursoSelect = document.getElementById('curso');
      cursoSelect.innerHTML = '<option value="" selected disabled hidden>Selecione um curso</option>';

      if (cursosPorProfessor[profId]) {
        for (const idUc in cursosPorProfessor[profId]) {
          const option = document.createElement('option');
          option.value = idUc;
          option.text = cursosPorProfessor[profId][idUc].nomeuc;
          cursoSelect.appendChild(option);
        }
      } else {
        alert('Esse professor não possui cursos cadastrados.');
      }

      const compSelect = document.getElementById('competencia');
      compSelect.innerHTML = '<option value="" disabled selected hidden>Selecione uma competência</option>';
    }

    function carregarCompetencias() {
      const profId = document.getElementById('nomeprof').value;
      const cursoId = document.getElementById('curso').value;
      const compSelect = document.getElementById('competencia');

      compSelect.innerHTML = '<option value="" disabled selected hidden>Selecione uma competência</option>';

      if (cursosPorProfessor[profId] && cursosPorProfessor[profId][cursoId]) {
        cursosPorProfessor[profId][cursoId].competencias.forEach(comp => {
          const option = document.createElement('option');
          option.value = comp.idcomp;
          option.text = comp.nomecomp;
          compSelect.appendChild(option);
        });
      }
    }

    function salvarAgenda() {
      const cursoId = document.getElementById('curso').value;
      const compId = document.getElementById('competencia').value;
      const dataInicio = document.getElementById('data_inicio').value;
      const dataFim = document.getElementById('data_fim').value;
      const profId = document.getElementById('nomeprof').value;

      if (!cursoId || !compId || !dataInicio || !dataFim) {
        alert("Preencha todos os campos.");
        return;
      }

      const cursoNome = cursosPorProfessor[profId][cursoId].nomeuc;
      const compNome = cursosPorProfessor[profId][cursoId].competencias.find(c => c.idcomp == compId).nomecomp;

      const inicio = new Date(dataInicio);
      const fim = new Date(dataFim);

      const cells = document.querySelectorAll('.calendar td');
      cells.forEach(cell => {
        const dayDiv = cell.querySelector('.day-number');
        if (!dayDiv) return;

        const dia = parseInt(dayDiv.textContent);
        const mesAtual = currentDate.getMonth();
        const anoAtual = currentDate.getFullYear();
        const dataCelula = new Date(anoAtual, mesAtual, dia);

        if (dataCelula >= inicio && dataCelula <= fim) {
          cell.style.backgroundColor = '#99bfe2ff';
          cell.innerHTML += `<div style="font-size: 12px; color: #0a0d8d; margin-top: 4px;"><b>${cursoNome}</b><br>${compNome}</div>`;
        }
      });
    }
  </script>
</body>
</html>
