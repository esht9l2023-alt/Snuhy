<div class="wrap snuhy-admin">
  <h1><?php esc_html_e('Links','snuhy'); ?></h1>

  <div class="snuhy-form">
    <h2><?php esc_html_e('Add / Edit Link','snuhy'); ?></h2>
    <div class="row">
      <label><?php esc_html_e('Keyword','snuhy'); ?></label>
      <input type="text" id="snuhy_kw" placeholder="مثال: iptv europe">
    </div>
    <div class="row">
      <label><?php esc_html_e('Target URL','snuhy'); ?></label>
      <input type="url" id="snuhy_url" placeholder="https://example.com/page">
    </div>
    <div class="row">
      <label>rel</label>
      <input type="text" id="snuhy_rel" placeholder="nofollow | sponsored | ugc">
    </div>
    <div class="row">
      <label><?php esc_html_e('Type','snuhy'); ?></label>
      <select id="snuhy_type">
        <option value="auto"><?php esc_html_e('Auto','snuhy'); ?></option>
        <option value="internal"><?php esc_html_e('Internal','snuhy'); ?></option>
        <option value="external"><?php esc_html_e('External','snuhy'); ?></option>
      </select>
    </div>
    <div class="actions">
      <button class="button button-primary" id="snuhy_save"><?php esc_html_e('Save','snuhy'); ?></button>
      <button class="button" id="snuhy_reset"><?php esc_html_e('Reset','snuhy'); ?></button>
      <input type="hidden" id="snuhy_id" value="">
    </div>
  </div>

  <div style="display:flex;gap:8px;align-items:center;margin:10px 0">
    <button class="button" id="snuhy_export"><?php esc_html_e('Export CSV','snuhy'); ?></button>
    <button class="button" id="snuhy_import_open"><?php esc_html_e('Import CSV','snuhy'); ?></button>
  </div>

  <div id="snuhy_import_modal" style="display:none;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px;max-width:760px">
    <h2 style="margin-top:0"><?php esc_html_e('Import CSV','snuhy'); ?></h2>
    <p><?php esc_html_e('Paste CSV with header: keyword,target_url[,rel,type,enabled]','snuhy'); ?></p>
    <textarea id="snuhy_import_text" rows="8" style="width:100%;font-family:monospace"></textarea>
    <div style="display:flex;gap:8px;margin-top:8px">
      <button class="button button-primary" id="snuhy_import_go"><?php esc_html_e('Import','snuhy'); ?></button>
      <button class="button" id="snuhy_import_close"><?php esc_html_e('Close','snuhy'); ?></button>
    </div>
  </div>

  <hr>

  <table class="widefat striped" id="snuhy_table">
    <thead><tr>
      <th>ID</th>
      <th><?php esc_html_e('Keyword','snuhy'); ?></th>
      <th><?php esc_html_e('Target URL','snuhy'); ?></th>
      <th><?php esc_html_e('Type','snuhy'); ?></th>
      <th>rel</th>
      <th><?php esc_html_e('Enabled','snuhy'); ?></th>
      <th><?php esc_html_e('Actions','snuhy'); ?></th>
    </tr></thead>
    <tbody><tr><td colspan="7"><?php esc_html_e('Loading...','snuhy'); ?></td></tr></tbody>
  </table>
</div>
