const fs = require('fs')
const path = '/c/Projetos/test_smartlead/frontend/src/views/Profile.vue'
const s = fs.readFileSync(path,'utf8')
const tMatch = s.match(/<template[^>]*>[\s\S]*?<\/template>/)
if(!tMatch){ console.error('No <template> found'); process.exit(1) }
const t = tMatch[0]
const tagRe = /<\/?([a-zA-Z0-9_:\-\.]+)([^>]*)>/g
const selfCloseRe = /\/>\s*$/
const voids = new Set(['area','base','br','col','embed','hr','img','input','keygen','link','meta','param','source','track','wbr'])
let m
const stack = []
let index = 0
while((m = tagRe.exec(t)) !== null){
  const raw = m[0]
  const name = m[1]
  const isClose = raw.startsWith('</')
  const isSelf = selfCloseRe.test(raw) || voids.has(name.toLowerCase())
  // ignore Vue templates like {{ }} and comments already excluded
  if(isSelf) continue
  if(!isClose){
    stack.push({name, pos: m.index})
  } else {
    if(stack.length === 0){
      console.error('Extra closing tag for', name, 'at', m.index)
      process.exit(2)
    }
    const top = stack[stack.length-1]
    if(top.name === name){
      stack.pop()
    } else {
      console.error('Mismatched closing tag', name, 'expected', top.name, 'at', m.index)
      process.exit(3)
    }
  }
}
if(stack.length) {
  console.error('Unclosed tags (top first):')
  stack.slice().reverse().forEach(t=> console.error(t.name, 'at', t.pos))
  process.exit(4)
}
console.log('All template tags balanced')
