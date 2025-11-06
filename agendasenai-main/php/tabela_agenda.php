<?php
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
    <div class="container">
        <div style="display:flex; gap:24px; align-items:flex-start;">

            <!-- 📅 CALENDÁRIO -->
            <div style="flex:1; min-width: 800px;">
                <div class="controls" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <button onclick="changeMonth(-1)" class="btn btn-primary">← Mês Anterior</button>
                    <h4 id="monthYear" style="margin:0;"></h4>
                    <button onclick="changeMonth(1)" class="btn btn-primary">Próximo Mês →</button>
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

            <!-- 🧾 LADO DIREITO -->
            <div style="width:360px; margin-left: 40px; margin-top: -50px" id="agendaFormWrapper">

                <!-- Seleção de Professor -->
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
                        <button id="btnEdit" class="btn btn-outline-primary">Inserir</button>
                    </div>
                    <small style="display:block; margin-top:6px; color:#666;">
                        Selecione um professor para ver a agenda. Clique em Inserir para abrir o formulário.
                    </small>
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
                        <label>Turno:</label>
                        <select name="turno" id="form_turno" class="form-select" required>
                            <option value="">-- selecione o turno --</option>
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

        const mainProfSelect = document.getElementById('main_prof_select');
        const btnEdit = document.getElementById('btnEdit');
        const formHiddenProf = document.getElementById('form_id_prof');
        const formUc = document.getElementById('form_id_uc');
        const formComp = document.getElementById('form_id_comp');
        const formTurno = document.getElementById('form_turno');
        const formSalvar = document.getElementById('formSalvar');
        const monthYear = document.getElementById('monthYear');
        const calendarBody = document.getElementById('calendarBody');

        let currentDate = new Date();

        // Seleção de professor
        mainProfSelect.addEventListener('change', () => {
            const val = mainProfSelect.value;
            window.location.href = val ? `tabela_agenda.php?id_prof=${val}` : 'tabela_agenda.php';
        });

        // Mostrar formulário
        btnEdit.addEventListener('click', e => {
            e.preventDefault();
            const val = mainProfSelect.value;
            if (!val) return alert('Selecione um professor antes de editar.');
            formHiddenProf.value = val;
            popularUCs(val);
            formSalvar.style.display = 'block';
            formSalvar.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });

        // Popular cursos
        function popularUCs(profId) {
            formUc.innerHTML = '<option value="">-- selecione a UC --</option>';
            formComp.innerHTML = '<option value="">-- selecione a competência --</option>';
            formTurno.innerHTML = '<option value="">-- selecione o turno --</option>';
            if (!cursosPorProfessor[profId]) return;
            for (const idUc in cursosPorProfessor[profId]) {
                const opt = document.createElement('option');
                opt.value = idUc;
                opt.textContent = cursosPorProfessor[profId][idUc].nomeuc;
                formUc.appendChild(opt);
            }
        }

        // Popular competências
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

        // ✅ Popular turno
        function popularTurno(profId, ucId) {
            formTurno.innerHTML = '<option value="">-- selecione o turno --</option>';
            if (!profId || !ucId) return;
            const turno = cursosPorProfessor[profId][ucId]?.turno;
            if (turno) {
                const opt = document.createElement('option');
                opt.value = turno;
                opt.textContent = turno;
                formTurno.appendChild(opt);
            }
        }

        formUc.addEventListener('change', function() {
            popularCompetencias(formHiddenProf.value, this.value);
            popularTurno(formHiddenProf.value, this.value);
        });

        window.addEventListener('load', function() {
            if (selectedProf) {
                mainProfSelect.value = selectedProf;
                formHiddenProf.value = selectedProf;
                popularUCs(selectedProf);
            }
            renderCalendar();
        });

        // 📅 Renderizar calendário
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

                    if (i === 0 && j < firstDay) {
                        // vazio
                    } else if (date > daysInMonth) {
                        // fim
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
                                    evDiv.innerHTML = `
                                        <b>${ev.nomeuc}</b><br>
                                        ${ev.nomecomp}<br>
                                        <small><i>${ev.turno || ''}</i></small><br>
                                        <div style="margin-top:4px; display:flex; gap:4px; justify-content:center;">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editarEvento(${ev.id}, '${thisDateStr}')">✏️</button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="excluirEvento(${ev.id}, '${thisDateStr}')">🗑️</button>
                                        </div>
                                    `;
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
