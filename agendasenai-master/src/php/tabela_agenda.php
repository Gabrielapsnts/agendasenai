<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Calendário Mensal em Grade</title>
   <link rel="stylesheet" href="../bootstrap/bootstrap.css" >
    <link rel="stylesheet" href="../css/tabela_agenda.css" >

</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div style= "background-color: #0a0d8d;" class="container-fluid d-flex justify-content-between align-items-center">
    <!-- Logo / Título -->
    <a class="navbar-brand" style="color:white" href="dashboard.php">AGENDA SENAI</a>


    <!-- Link de logout -->
    <ul class="navbar-nav">
<li class="nav-item">
       <a class="nav-link active"style="color:white" aria-current="page" href="tabela_uc.php">Cursos</a>
 </li>
       <li class="nav-item">
        <a class="nav-link active"style="color:white" aria-current="page" href="tabela_prof.php">Professores</a>
 </li>
      <li class="nav-item">
        <a class="nav-link active"style="color:white" aria-current="page" href="logout.php">Logout</a>
      </li>
    </ul>
  </div>
</nav>

  <div class="calendar-container">
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
      <tbody id="calendarBody">
        <!-- Dias serão gerados aqui -->
      </tbody>
    </table>
  </div>

  <script>
    const monthYear = document.getElementById('monthYear');
    const calendarBody = document.getElementById('calendarBody');
    let currentDate = new Date();

    function renderCalendar() {
      const year = currentDate.getFullYear();
      const month = currentDate.getMonth();
      const today = new Date();

      const monthNames = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                          'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

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

            // Aqui você pode adicionar elementos interativos (ex: input, div para tarefas, etc)

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
