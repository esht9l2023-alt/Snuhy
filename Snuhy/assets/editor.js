(function(){
  if (!window.wp) return;
  const { registerPlugin } = wp.plugins || {};
  const { PluginSidebar } = wp.editPost || {};
  const { PanelBody, Button, Spinner, Notice } = wp.components || {};
  const { useSelect } = wp.data || {};
  const { createElement: h, useEffect, useState } = wp.element || {};

  if (!registerPlugin || !PluginSidebar) return;

  function fetchJSON(url, opts={}){
    return fetch(url, Object.assign({
      headers: { 'X-WP-Nonce': (window.SnuhyVars && SnuhyVars.nonce) || '' },
      credentials: 'same-origin'
    }, opts)).then(r => r.json());
  }

  function getTextFromBlocks(blocks){
    let text = '';
    (blocks||[]).forEach(b=>{
      if (b.attributes && b.attributes.content) text += ' ' + b.attributes.content;
      if (b.innerBlocks && b.innerBlocks.length) text += ' ' + getTextFromBlocks(b.innerBlocks);
    });
    return text;
  }

  const Sidebar = () => {
    const [loading, setLoading] = useState(true);
    const [links, setLinks] = useState([]);
    const [suggestions, setSuggestions] = useState([]);
    const [err, setErr] = useState('');

    const post = useSelect( select => {
      return {
        blocks: (select('core/block-editor') && select('core/block-editor').getBlocks()) || []
      };
    }, []);

    useEffect(()=>{
      setLoading(true);
      fetchJSON((SnuhyVars && SnuhyVars.rest) ? (SnuhyVars.rest + '/links') : '/wp-json/snuhy/v1/links')
        .then(data=>{
          setLinks(Array.isArray(data)?data:[]);
          setLoading(false);
        })
        .catch(e=>{ setErr('Failed to load Snuhy links.'); setLoading(false); });
    }, []);

    useEffect(()=>{
      if (!links.length) { setSuggestions([]); return; }
      const txt = (post && post.blocks) ? getTextFromBlocks(post.blocks).toLowerCase() : '';
      const found = [];
      links.forEach(l=>{
        const kw = (l.keyword||'').toLowerCase();
        if (!kw) return;
        if (txt.includes(kw)){
          // naive: يفترض عدم وجود رابط — (v2 هنحلل HTML)
          found.push({kw, url:l.target_url, type:l.type});
        }
      });
      setSuggestions(found);
    }, [links, post]);

    return h(
      PluginSidebar,
      { name:'snuhy-sidebar', title:'Snuhy Suggestions', icon:'admin-links' },
      h(PanelBody, { title:'Suggested Anchors', initialOpen:true },
        loading ? h(Spinner, {}) :
        err ? h(Notice, {status:'error', isDismissible:false}, err) :
        (suggestions.length ? suggestions.map((s,i)=>
          h('div', { key:i, style:{marginBottom:'8px'}},
            h('div', null, h('strong', null, s.kw)),
            h('div', {style:{fontSize:'12px',opacity:.8}}, s.url),
            h(Button, { isSecondary:true, onClick:()=>copy(s.kw) }, 'Copy anchor')
          )
        ) : h('p', null, 'No suggestions.'))
      )
    );
  };

  function copy(t){
    try { navigator.clipboard.writeText(t); } catch(e){}
  }

  registerPlugin('snuhy-sidebar', { render: Sidebar, icon: 'admin-links' });
})();
