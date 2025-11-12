<?php
include "conexao_db.php"; // Conexão MySQLi

// 🔹 Buscar professores
$sqlProf = "SELECT id_prof, nomeprof FROM professor ORDER BY nomeprof";
$resProf = $conn->query($sqlProf);
$professores = [];
while ($row = $resProf->fetch_assoc()) {
    $professores[] = $row;
}

// 🔹 Buscar todas as turmas com UC
$sqlTurmas = "
    SELECT 
        t.id_turma, t.nome_turma, t.turno, t.iduc, uc.nomeuc
    FROM turma t
    JOIN uc ON t.iduc = uc.iduc
    ORDER BY uc.nomeuc, t.nome_turma
";
$resTurmas = $conn->query($sqlTurmas);
$turmas = [];
while ($row = $resTurmas->fetch_assoc()) {
    $turmas[] = $row;
}

// 🔹 Buscar eventos (agenda)
include "../includes/buscar_dados.php";
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
<?php include "../includes/navbar.php"; ?>

<div class="container" style="display:flex; justify-content:space-between; align-items:flex-start; gap:30px;">

  <!-- 📅 LADO ESQUERDO -->
  <div style="flex:1;">
    <!-- Cabeçalho -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <label style="font-weight:bold;">Professor:</label>
        <select id="selectProfessor" class="form-select" style="width:250px;">
          <option value="">-- selecione o professor --</option>
          <?php foreach ($professores as $p): ?>
            <option value="<?= $p['id_prof'] ?>" <?= isset($_GET['id_prof']) && $_GET['id_prof'] == $p['id_prof'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($p['nomeprof']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <button id="btnInserir" class="btn btn-primary">➕ Inserir horário</button>
    </div>

  <!-- Cabeçalho do calendário -->
<div class="calendar-header">
  <button id="prevBtn" onclick="changeMonth(-1)" class="btn btn-outline-secondary">←</button>
  <h4 id="monthYear"></h4>
  <button id="nextBtn" onclick="changeMonth(1)" class="btn btn-outline-secondary">→</button>
</div>




    <!-- Calendário -->
    <table class="calendar" style="width:100%;">
      <thead>
        <tr>
          <th>Dom</th><th>Seg</th><th>Ter</th><th>Qua</th><th>Qui</th><th>Sex</th><th>Sáb</th>
        </tr>
      </thead>
      <tbody id="calendarBody"></tbody>
    </table>
  </div>

  <!-- 🧾 FORMULÁRIO DE INSERÇÃO -->
 <div id="agendaFormWrapper" style="width:360px; align-self:flex-start; position:sticky; top:100px;" hidden>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
      <h5 style="margin:0;">Inserir novo horário</h5>
      <button id="btnFecharForm" class="btn btn-sm btn-outline-danger">✖</button>
    </div>

    <form action="salvar_agenda.php" method="POST" id="formSalvar">
      <input type="hidden" name="id_uc" id="form_id_uc">
      <input type="hidden" name="id_evento" id="form_id_evento">
      <input type="hidden" name="dia_edicao" id="form_dia_edicao">

      <!-- Turma -->
      <div class="form-group">
        <label>Turma:</label>
        <select name="id_turma" id="form_id_turma" class="form-select" required>
          <option value="">-- selecione a turma --</option>
          <?php foreach ($turmas as $t): ?>
            <option value="<?= $t['id_turma'] ?>" data-uc="<?= $t['iduc'] ?>" data-turno="<?= $t['turno'] ?>">
              <?= htmlspecialchars($t['nome_turma']) ?> (<?= $t['nomeuc'] ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Professor -->
      <div class="form-group">
        <label>Professor:</label>
        <select name="id_prof" id="form_id_prof" class="form-select" required>
          <option value="">-- selecione o professor --</option>
          <?php foreach ($professores as $p): ?>
            <option value="<?= $p['id_prof'] ?>"><?= htmlspecialchars($p['nomeprof']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Competência -->
      <div class="form-group">
        <label>Competência:</label>
        <select name="id_comp" id="form_id_comp" class="form-select" required>
          <option value="">-- selecione a competência --</option>
        </select>
      </div>

      <!-- Turno -->
      <div class="form-group">
        <label>Turno:</label>
        <select name="turno" id="form_turno" class="form-select" required>
          <option value="">-- selecione o turno --</option>
        </select>
      </div>

      <!-- Datas -->
      <div class="form-group">
        <label>Data Início:</label>
        <input type="date" name="data_inicio" id="form_data_inicio" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Data Fim:</label>
        <input type="date" name="data_fim" id="form_data_fim" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-primary w-100">Salvar</button>
    </form>
  </div>
</div>

<script>
const eventos = <?php echo json_encode($eventos); ?>;
const monthYear = document.getElementById('monthYear');
const calendarBody = document.getElementById('calendarBody');
const selectProfessor = document.getElementById('selectProfessor');
const btnInserir = document.getElementById('btnInserir');
const agendaFormWrapper = document.getElementById('agendaFormWrapper');
const btnFecharForm = document.getElementById('btnFecharForm');
const formIdTurma = document.getElementById('form_id_turma');
const formIdUc = document.getElementById('form_id_uc');
const formIdComp = document.getElementById('form_id_comp');
  const formTurno = document.getElementById('form_turno');
  const formIdProf = document.getElementById('form_id_prof');
const formIdEvento = document.getElementById('form_id_evento');
const formDiaEdicao = document.getElementById('form_dia_edicao');
const formDataInicio = document.getElementById('form_data_inicio');
const formDataFim = document.getElementById('form_data_fim');

let currentDate = new Date();

// ✅ Muda professor -> recarrega a agenda
selectProfessor.addEventListener('change', () => {
  const val = selectProfessor.value;
  window.location.href = val ? `tabela_agenda.php?id_prof=${val}` : 'tabela_agenda.php';
});

// ✅ Mostrar/ocultar formulário
btnInserir.addEventListener('click', () => {
  agendaFormWrapper.hidden = !agendaFormWrapper.hidden;
  if (!agendaFormWrapper.hidden) {
    formIdProf.value = selectProfessor.value;
  }
});
btnFecharForm.addEventListener('click', () => agendaFormWrapper.hidden = true);

// ✅ Quando muda a turma, preencher UC, turno e competências
formIdTurma.addEventListener('change', () => {
  const turmaSel = formIdTurma.options[formIdTurma.selectedIndex];
  if (!turmaSel) return;

  const idUc = turmaSel.getAttribute('data-uc');
  const turno = turmaSel.getAttribute('data-turno');
  formIdUc.value = idUc;

  // Preenche o turno
  formTurno.innerHTML = '';
  if (turno) {
    const opt = document.createElement('option');
    opt.value = turno;
    opt.textContent = turno;
    formTurno.appendChild(opt);
  }

  // Limpa e busca competências da UC via AJAX
  formIdComp.innerHTML = '<option value="">Carregando...</option>';
  fetch(`buscar_competencias_uc.php?iduc=${idUc}`)


    .then(res => {
      if (!res.ok) throw new Error('Erro ao buscar competências');
      return res.json();
    })
    .then(data => {
      formIdComp.innerHTML = '<option value="">-- selecione a competência --</option>';
      if (data.length === 0) {
        const opt = document.createElement('option');
        opt.textContent = 'Nenhuma competência encontrada';
        formIdComp.appendChild(opt);
        return;
      }
      data.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.idcomp;
        opt.textContent = c.nomecomp;
        formIdComp.appendChild(opt);
      });
    })
    .catch(err => {
      console.error('Erro ao carregar competências:', err);
      formIdComp.innerHTML = '<option value="">Erro ao carregar</option>';
    });
});


// ✅ Renderização do calendário
function renderCalendar() {
  const year = currentDate.getFullYear();
  const month = currentDate.getMonth();
  const today = new Date();
  const monthNames = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                      'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
  monthYear.textContent = `${monthNames[month]} ${year}`;
  calendarBody.innerHTML = '';
  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();
  let date = 1;
  for (let i = 0; i < 6; i++) {
    const tr = document.createElement('tr');
    for (let j = 0; j < 7; j++) {
      const td = document.createElement('td');
      if (i === 0 && j < firstDay) { tr.appendChild(td); continue; }
      if (date > daysInMonth) break;

      const divNum = document.createElement('div');
      divNum.className = 'day-number';
      divNum.textContent = date;
      td.appendChild(divNum);

      const thisDateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
      eventos.forEach(ev => {
        if (ev.data_inicio <= thisDateStr && thisDateStr <= ev.data_fim) {
          const evDiv = document.createElement('div');
          evDiv.className = 'event';
          evDiv.innerHTML = `
            <b>${ev.nomeuc}</b><br>
            <small>Turma: ${ev.nome_turma || '-'}</small><br>
            ${ev.nomecomp}<br>
            <i>${ev.turno || ''}</i>
          `;
          // adicionar botões Editar / Excluir
          const actions = document.createElement('div');
          actions.className = 'event-actions';

          const btnEdit = document.createElement('button');
          btnEdit.type = 'button';
          btnEdit.className = 'btn btn-sm btn-outline-primary';
          btnEdit.style.marginRight = '6px';
          btnEdit.textContent = 'Editar';
          btnEdit.dataset.id = ev.id;
          btnEdit.dataset.date = thisDateStr;
          btnEdit.addEventListener('click', onEditClick);

          const btnDel = document.createElement('button');
          btnDel.type = 'button';
          btnDel.className = 'btn btn-sm btn-outline-danger';
          btnDel.textContent = 'Excluir';
          btnDel.dataset.id = ev.id;
          btnDel.dataset.date = thisDateStr;
          btnDel.addEventListener('click', onDeleteClick);

          actions.appendChild(btnEdit);
          actions.appendChild(btnDel);
          evDiv.appendChild(actions);

          td.appendChild(evDiv);
        }
      });

      if (date === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
        td.classList.add('today');
      }
      tr.appendChild(td);
      date++;
    }
    calendarBody.appendChild(tr);
  }
}

// Carrega competências via idUc e seleciona um id_comp se passado
function loadCompetenciasByUc(idUc, selectCompId) {
  return fetch(`buscar_competencias_uc.php?iduc=${idUc}`)
    .then(res => {
      if (!res.ok) throw new Error('Erro ao buscar competências');
      return res.json();
    })
    .then(data => {
      formIdComp.innerHTML = '<option value="">-- selecione a competência --</option>';
      data.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.idcomp;
        opt.textContent = c.nomecomp;
        formIdComp.appendChild(opt);
      });
      if (selectCompId) formIdComp.value = selectCompId;
    })
    .catch(err => {
      console.error('Erro ao carregar competências:', err);
      formIdComp.innerHTML = '<option value="">Erro ao carregar</option>';
    });
}

// Handler editar: abre formulário preenchido para editar apenas o dia clicado
function onEditClick(e) {
  const id = e.currentTarget.dataset.id;
  const dia = e.currentTarget.dataset.date;
  const ev = eventos.find(x => String(x.id) === String(id));
  if (!ev) return alert('Evento não encontrado para edição');

  // Mostrar formulário e preencher
  agendaFormWrapper.hidden = false;
  formIdEvento.value = ev.id;
  formDiaEdicao.value = dia;

  // Preencher campos principais
  formIdProf.value = ev.id_prof;
  formTurno.innerHTML = '';
  if (ev.turno) {
    const opt = document.createElement('option'); opt.value = ev.turno; opt.textContent = ev.turno; formTurno.appendChild(opt);
  }

  // Se a turma existir, seleciona e usa a UC dela; senão usa id_uc direto
  if (ev.id_turma) {
    formIdTurma.value = ev.id_turma;
    const turmaSel = formIdTurma.options[formIdTurma.selectedIndex];
    const idUc = turmaSel ? turmaSel.getAttribute('data-uc') : ev.id_uc;
    if (idUc) {
      formIdUc.value = idUc;
      loadCompetenciasByUc(idUc, ev.id_comp);
    }
  } else {
    formIdTurma.value = '';
    formIdUc.value = ev.id_uc;
    loadCompetenciasByUc(ev.id_uc, ev.id_comp);
  }

  // Ambos campos de data ficam inicialmente no dia específico
  formDataInicio.value = dia;
  formDataFim.value = dia;
  // Ajusta o professor select
  formIdProf.value = ev.id_prof;
}

// Handler excluir: chama excluir_evento.php e recarrega a página após sucesso
function onDeleteClick(e) {
  if (!confirm('Confirma exclusão deste dia do evento?')) return;
  const id = e.currentTarget.dataset.id;
  const dia = e.currentTarget.dataset.date;
  const fd = new FormData();
  fd.append('id', id);
  fd.append('dia', dia);

  fetch('excluir_evento.php', { method: 'POST', body: fd })
    .then(res => res.text())
    .then(txt => {
      alert(txt);
      // Recarrega para atualizar calendário e dados do servidor
      window.location.reload();
    })
    .catch(err => {
      console.error('Erro ao excluir:', err);
      alert('Erro ao excluir evento');
    });
}
function changeMonth(offset) {
  currentDate.setMonth(currentDate.getMonth() + offset);
  renderCalendar();
}
window.addEventListener('load', renderCalendar);
</script>

</body>
</html>
