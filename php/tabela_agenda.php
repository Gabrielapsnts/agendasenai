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
      <form action="agenda_professor.php" method="POST">
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
      </form>
    </div>
  </div>

  <script>
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

            // Aqui dá pra adicionar eventos individuais depois
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
</body>
</html>
