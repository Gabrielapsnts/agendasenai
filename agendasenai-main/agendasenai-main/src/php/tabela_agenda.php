<?php
include "../includes/buscar_dados.php";
include "../includes/navbar.php";
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
    <div class="container">
        <div style="display:flex; gap:24px; align-items:flex-start;">

            <!-- Calendário -->
            <div style="flex:1; min-width: 800px;">
                <div class="controls" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <button onclick="changeMonth(-1)" class="btn btn-primary">← Mês Anterior</button>
                    <h4 id="monthYear" style="margin:0;"></h4>
                    <button onclick="changeMonth(1)" class="btn btn-primary">Próximo Mês →</button>
                </div>
                <table class="calendar" style="width: 100%;">
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

            <!-- Lateral direita -->
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
   
        // FUNÇÕES DE EDIÇÃO / EXCLUSÃO

        function editarEvento(id, dia) {
            const ev = eventos.find(e => e.id == id);
            if (!ev) return alert('Evento não encontrado.');

            formSalvar.style.display = 'block';
            formHiddenProf.value = ev.id_prof;
            mainProfSelect.value = ev.id_prof;
            popularUCs(ev.id_prof);

            setTimeout(() => {
                formUc.value = ev.id_uc;
                popularCompetencias(ev.id_prof, ev.id_uc);
                formComp.value = ev.id_comp;
            }, 100);

            formSalvar.querySelector('[name="data_inicio"]').value = dia;
            formSalvar.querySelector('[name="data_fim"]').value = dia;

            let hiddenId = formSalvar.querySelector('input[name="id_evento"]');
            if (!hiddenId) {
                hiddenId = document.createElement('input');
                hiddenId.type = 'hidden';
                hiddenId.name = 'id_evento';
                formSalvar.appendChild(hiddenId);
            }
            hiddenId.value = id;

            let hiddenDia = formSalvar.querySelector('input[name="dia_edicao"]');
            if (!hiddenDia) {
                hiddenDia = document.createElement('input');
                hiddenDia.type = 'hidden';
                hiddenDia.name = 'dia_edicao';
                formSalvar.appendChild(hiddenDia);
            }
            hiddenDia.value = dia;

            formSalvar.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        function excluirEvento(id, dia) {
            if (!confirm('Deseja excluir somente este dia do evento?')) return;
            fetch('excluir_evento.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `id=${id}&dia=${dia}`
            })
            .then(r => r.text())
            .then(txt => {
                alert(txt);
                location.reload();
            })
            .catch(err => alert('Erro ao excluir: ' + err));
        }

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const today = new Date();

            const monthNames = [
                'Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'
            ];

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
                                    evDiv.innerHTML = `
                                        <b>${ev.nomeuc}</b><br>
                                        ${ev.nomecomp}<br>
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
