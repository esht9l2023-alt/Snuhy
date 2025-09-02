(function($){
  const api = (path, opts={}) => fetch(SnuhyVars.rest + path, {
    method: opts.method || 'GET',
    headers: {
      'X-WP-Nonce': SnuhyVars.nonce,
      'Content-Type': 'application/json'
    },
    body: opts.body ? JSON.stringify(opts.body) : undefined,
    credentials: 'same-origin'
  }).then(r => r.json());

  const table = $('#snuhy_table tbody');
  const idEl = $('#snuhy_id');
  const kwEl = $('#snuhy_kw');
  const urlEl= $('#snuhy_url');
  const relEl= $('#snuhy_rel');
  const typeEl= $('#snuhy_type');

  function renderRows(rows){
    if (!Array.isArray(rows) || !rows.length){
      table.html('<tr><td colspan="7">No links.</td></tr>');
      return;
    }
    const html = rows.map(r => {
      return `<tr data-id="${r.id}">
        <td>${r.id}</td>
        <td>${escapeHtml(r.keyword||'')}</td>
        <td><a href="${escapeAttr(r.target_url||'')}" target="_blank">${escapeHtml(r.target_url||'')}</a></td>
        <td>${escapeHtml(r.type||'')}</td>
        <td>${escapeHtml(r.rel||'')}</td>
        <td>${r.enabled ? '✓' : '—'}</td>
        <td class="snuhy-action">
          <button class="button snuhy-edit">Edit</button>
          <button class="button snuhy-toggle">${r.enabled ? 'Disable' : 'Enable'}</button>
          <button class="button button-link-delete snuhy-del">Delete</button>
        </td>
      </tr>`;
    }).join('');
    table.html(html);
  }

  function load(){ api('/links').then(renderRows); }
  function resetForm(){ idEl.val(''); kwEl.val(''); urlEl.val(''); relEl.val(''); typeEl.val('auto'); }

  $('#snuhy_save').on('click', function(){
    const payload = {
      keyword: kwEl.val().trim(),
      target_url: urlEl.val().trim(),
      rel: relEl.val().trim()
    };
    const t = typeEl.val(); if (t !== 'auto') payload.type = t;
    const id = idEl.val();
    const req = id ? api('/links/'+id, {method:'PUT', body: payload})
                   : api('/links', {method:'POST', body: payload});
    req.then(()=>{ resetForm(); load(); });
  });
  $('#snuhy_reset').on('click', resetForm);

  table.on('click','.snuhy-edit', function(){
    const tr = $(this).closest('tr');
    idEl.val(tr.data('id'));
    kwEl.val($('td:nth-child(2)',tr).text().trim());
    urlEl.val($('td:nth-child(3) a',tr).attr('href'));
    relEl.val($('td:nth-child(5)',tr).text().trim());
    typeEl.val($('td:nth-child(4)',tr).text().trim() || 'auto');
    window.scrollTo({top:0,behavior:'smooth'});
  });
  table.on('click','.snuhy-del', function(){
    const id = $(this).closest('tr').data('id');
    if (!confirm('Delete this link?')) return;
    api('/links/'+id, {method:'DELETE'}).then(load);
  });
  table.on('click','.snuhy-toggle', function(){
    const id = $(this).closest('tr').data('id');
    api('/links/'+id+'/toggle', {method:'POST'}).then(load);
  });

  // Export
  $('#snuhy_export').on('click', function(){
    const url = SnuhyVars.rest + '/links/export';
    const a = document.createElement('a');
    a.href = url;
    a.download = 'snuhy-links.csv';
    document.body.appendChild(a);
    a.click();
    a.remove();
  });

  // Import (paste CSV)
  $('#snuhy_import_open').on('click', ()=> $('#snuhy_import_modal').show());
  $('#snuhy_import_close').on('click', ()=> $('#snuhy_import_modal').hide());
  $('#snuhy_import_go').on('click', async function(){
    const txt = $('#snuhy_import_text').val().trim();
    if (!txt) return alert('Paste CSV first.');
    const rows = parseCSV(txt);
    if (!rows.length) return alert('No rows.');
    for (const r of rows){
      if (!r.keyword || !r.target_url) continue;
      const body = { keyword: r.keyword, target_url: r.target_url };
      if (r.rel) body.rel = r.rel;
      if (r.type && r.type!=='auto') body.type = r.type;
      // enabled handled by default=1; skip for now
      await api('/links', {method:'POST', body});
    }
    $('#snuhy_import_modal').hide();
    $('#snuhy_import_text').val('');
    load();
  });

  function parseCSV(s){
    // بسيط: يفترض صف هيدر. يفصل بفواصل. يدعم "قيم,بداخل,علامات اقتباس"
    const lines = s.split(/\r?\n/).filter(Boolean);
    if (!lines.length) return [];
    const headers = splitCSVLine(lines[0]).map(h => h.trim().toLowerCase());
    const out = [];
    for (let i=1;i<lines.length;i++){
      const cols = splitCSVLine(lines[i]);
      const row = {};
      headers.forEach((h,idx)=> row[h] = (cols[idx]||'').trim());
      out.push(row);
    }
    return out;
  }
  function splitCSVLine(line){
    const res = []; let cur = ''; let inQ = false;
    for (let i=0;i<line.length;i++){
      const c = line[i];
      if (c === '"' ){
        if (inQ && line[i+1] === '"'){ cur += '"'; i++; }
        else inQ = !inQ;
      } else if (c === ',' && !inQ){
        res.push(cur); cur = '';
      } else {
        cur += c;
      }
    }
    res.push(cur);
    return res;
  }

  function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m])); }
  function escapeAttr(s){ return escapeHtml(s); }

  load();
})(jQuery);
