@extends('layouts.app')

@section('title', 'Skill Tree')
@section('page_title', 'Tech Web / Skill Tree')

@section('header_actions')
<div class="flex items-center gap-3 bg-[#161b22] px-3 py-1 rounded-full border border-[#30363d]">
    <span class="text-[10px] font-bold text-amber-500 uppercase">
        Skill Points: <span id="skillPoints">05</span>
    </span>
</div>
@endsection

@section('content')

<!-- ================= STYLES ================= -->
<style>
html,
body {
    height: 100%;
    overflow: hidden;
}

.skill-node {
    position: absolute;
    width: 64px;
    height: 64px;
    border-radius: 50%;
    border: 2px solid #30363d;
    background: #0d1117;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #8b949e;
    cursor: pointer;
    transition: all .25s ease;
}

.skill-node:hover {
    border-color: #6366f1;
    color: #6366f1;
    transform: scale(1.15);
}

.tooltip {
    position: absolute;
    bottom: -32px;
    background: #161b22;
    border: 1px solid #30363d;
    padding: 4px 8px;
    font-size: 10px;
    border-radius: 6px;
    white-space: nowrap;
    pointer-events: none;
}

.skill-node {
    position: absolute;
    width: 70px;
    height: 70px;
    border-radius: 50%;
    border: 2px solid #30363d;
    background: radial-gradient(circle at top, #161b22, #0d1117);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #8b949e;
    cursor: pointer;
    transition: all .25s ease;
}

.skill-node:hover {
    border-color: #6366f1;
    color: #c7d2fe;
    box-shadow: 0 0 18px rgba(99, 102, 241, .6);
    transform: scale(1.15);
}

.skill-node.core {
    border-color: #22c55e;
    box-shadow: 0 0 22px rgba(34, 197, 94, .7);
    color: #bbf7d0;
}

.skill-node.advanced {
    border-color: #f59e0b;
    box-shadow: 0 0 22px rgba(245, 158, 11, .6);
    color: #fde68a;
}

/* ===== Custom Scrollbar ===== */
#skillScroll::-webkit-scrollbar {
    width: 10px;
}

#skillScroll::-webkit-scrollbar-track {
    background: #0d1117;
}

#skillScroll::-webkit-scrollbar-thumb {
    background: #30363d;
    border-radius: 8px;
    border: 2px solid #0d1117;
}

#skillScroll::-webkit-scrollbar-thumb:hover {
    background: #6366f1;
}

/* Firefox */


</style>

<!-- ================= TREE CONTAINER ================= -->
<div class="w-full h-[calc(100vh-64px)] bg-[#0d1117] text-white relative overflow-hidden">

    <!-- INTERNAL SCROLL AREA -->
    <div id="skillScroll" class="relative w-full h-full overflow-y-auto overflow-x-hidden">

        <!-- SKILL TREE CAN BE TALL -->
        <div id="skillTree" class="relative w-full min-h-[1200px]"></div>

    </div>
</div>



<!-- ================= MODAL ================= -->
<div id="skillModal" class="fixed inset-0 hidden items-center justify-center bg-black/70 z-50">
    <div class="bg-[#161b22] border border-[#30363d] rounded-xl p-6 max-w-md w-full">
        <h2 id="modalTitle" class="text-lg font-bold mb-2"></h2>
        <p id="modalDesc" class="text-sm text-[#8b949e]"></p>
        <div class="text-right mt-6">
            <button onclick="closeModal()" class="bg-indigo-500 px-4 py-2 rounded-lg text-sm">
                Close
            </button>
        </div>
    </div>
</div>

<!-- ================= SKILL DATA ================= -->
<script>
const skills = [
    /* ================= CORE ================= */
    {
        id: 'root',
        title: 'Foundations',
        icon: 'fa-code',
        x: 50,
        y: 80,
        tier: 'core',
        description: `
The absolute core of all tech disciplines.

You learn:
• Logical thinking
• Problem solving
• How computers actually work
• Basic algorithms and data flow

Without this, everything else collapses.
`
    },

    /* ================= WEB DEV ================= */
    {
        id: 'html',
        title: 'HTML',
        icon: 'fa-html5',
        x: 25,
        y: 260,
        parent: 'root',
        description: `
The skeleton of the web.

You learn:
• Semantic structure
• Accessibility
• SEO-friendly layouts
• How browsers read documents
`
    },
    {
        id: 'css',
        title: 'CSS',
        icon: 'fa-css3-alt',
        x: 15,
        y: 420,
        parent: 'html',
        description: `
The visual language of the web.

You master:
• Layout systems (Flexbox, Grid)
• Responsive design
• Animations
• Design consistency
`
    },
    {
        id: 'javascript',
        title: 'JavaScript',
        icon: 'fa-js',
        x: 35,
        y: 420,
        parent: 'html',
        description: `
The brain of modern web apps.

You unlock:
• DOM manipulation
• Events
• Async programming
• APIs & frameworks
`
    },
    {
        id: 'web-advanced',
        title: 'Full-Stack Web',
        icon: 'fa-layer-group',
        x: 25,
        y: 600,
        parent: 'javascript',
        tier: 'advanced',
        description: `
You become a full-stack developer.

Skills gained:
• Frontend frameworks
• Backend APIs
• Auth systems
• Databases
• Deployment
`
    },

    /* ================= APP DEV ================= */
    {
        id: 'mobile',
        title: 'App Development',
        icon: 'fa-mobile-screen',
        x: 55,
        y: 260,
        parent: 'root',
        description: `
Building software for phones and tablets.

Focus:
• Native vs cross-platform
• Performance constraints
• UX for small screens
`
    },
    {
        id: 'flutter',
        title: 'Flutter / React Native',
        icon: 'fa-react',
        x: 55,
        y: 440,
        parent: 'mobile',
        description: `
Cross-platform app development.

You learn:
• Component-based UI
• State management
• Native integrations
`
    },
    {
        id: 'app-advanced',
        title: 'Production Apps',
        icon: 'fa-rocket',
        x: 55,
        y: 620,
        parent: 'flutter',
        tier: 'advanced',
        description: `
Real-world deployment skills.

Includes:
• App store releases
• CI/CD
• Crash reporting
• Analytics
`
    },

    /* ================= CYBERSEC ================= */
    {
        id: 'networking',
        title: 'Networking',
        icon: 'fa-network-wired',
        x: 75,
        y: 260,
        parent: 'root',
        description: `
How data moves.

You understand:
• TCP/IP
• DNS
• HTTP/HTTPS
• Firewalls & routing
`
    },
    {
        id: 'linux',
        title: 'Linux',
        icon: 'fa-linux',
        x: 85,
        y: 420,
        parent: 'networking',
        description: `
The OS behind servers and hacking labs.

Skills:
• CLI mastery
• Permissions
• Services
• Automation
`
    },
    {
        id: 'cyber',
        title: 'Cybersecurity',
        icon: 'fa-user-secret',
        x: 75,
        y: 600,
        parent: 'linux',
        tier: 'advanced',
        description: `
Offense and defense.

You explore:
• Vulnerabilities
• Pen-testing
• Threat modeling
• Defensive security
`
    },

    /* ================= DESIGN ================= */
    {
        id: 'design',
        title: 'UI / UX Design',
        icon: 'fa-pen-nib',
        x: 40,
        y: 260,
        parent: 'root',
        description: `
Design that actually works.

You learn:
• User psychology
• Visual hierarchy
• Accessibility
`
    },
    {
        id: 'design-systems',
        title: 'Design Systems',
        icon: 'fa-palette',
        x: 40,
        y: 440,
        parent: 'design',
        description: `
Scalable design.

Includes:
• Components
• Tokens
• Consistency
• Collaboration with devs
`
    }
];
</script>


<!-- ================= TREE RENDER ================= -->
<script>
const tree = document.getElementById('skillTree');

/* SVG */
const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
svg.setAttribute('class', 'absolute top-0 left-0 w-full h-full');
tree.appendChild(svg);

/* Render Nodes */
skills.forEach(skill => {
    const node = document.createElement('div');
    node.className = 'skill-node';
    node.style.left = `calc(${skill.x}% - 32px)`;
    node.style.top = `${skill.y}px`;

    if (skill.tier) {
        node.classList.add(skill.tier);
    }

    node.innerHTML = `<i class="fa-solid ${skill.icon}"></i>`;
    tree.appendChild(node);

    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip hidden';
    tooltip.innerText = skill.title;
    node.appendChild(tooltip);

    node.onmouseenter = () => tooltip.classList.remove('hidden');
    node.onmouseleave = () => tooltip.classList.add('hidden');
    node.onclick = () => openModal(skill);

    if (skill.parent) {
        const parent = skills.find(s => s.id === skill.parent);
        drawLine(parent, skill);
    }
});

/* Lines */
function drawLine(from, to) {
    const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
    line.setAttribute('x1', `${from.x}%`);
    line.setAttribute('y1', from.y + 32);
    line.setAttribute('x2', `${to.x}%`);
    line.setAttribute('y2', to.y + 32);
    line.setAttribute('stroke', '#30363d');
    line.setAttribute('stroke-width', '2');
    svg.appendChild(line);
}
</script>

<!-- ================= MODAL LOGIC ================= -->
<script>
function openModal(skill) {
    modalTitle.innerText = skill.title;
    modalDesc.innerText = skill.description;
    skillModal.classList.remove('hidden');
    skillModal.classList.add('flex');
}

function closeModal() {
    skillModal.classList.add('hidden');
    skillModal.classList.remove('flex');
}
</script>


<!-- ===== FIREBASE ===== -->
<script type="module">
/* ================= FIREBASE IMPORTS ================= */
import {
    initializeApp
} from "https://www.gstatic.com/firebasejs/10.8.1/firebase-app.js";
import {
    getDatabase,
    ref,
    get,
    set
} from "https://www.gstatic.com/firebasejs/10.8.1/firebase-database.js";

/* ================= FIREBASE CONFIG ================= */
const firebaseConfig = {
    apiKey: "AIzaSyDslbJXnpvF6TtqRBGRCJhf2UYQrVeENYY",
    authDomain: "lvlup-e65d8.firebaseapp.com",
    databaseURL: "https://lvlup-e65d8-default-rtdb.firebaseio.com",
    projectId: "lvlup-e65d8",
    storageBucket: "lvlup-e65d8.firebasestorage.app",
    messagingSenderId: "480659587636",
    appId: "1:480659587636:web:86676bfc85fd28a8b34061"
};

/* ================= INIT ================= */
const app = initializeApp(firebaseConfig);
const db = getDatabase(app);

/* ================= LOAD SKILLS ================= */
const skillsRef = ref(db, 'skills');

get(skillsRef).then(snapshot => {
    if (!snapshot.exists()) {
        console.warn('No skills found in database');
        return;
    }

    const skills = snapshot.val();

    document.querySelectorAll('[data-skill]').forEach(node => {
        const skillName = node.dataset.skill;

        if (!skills[skillName]) return;

        if (skills[skillName].unlocked) {
            node.classList.remove('locked', 'available');
            node.classList.add('unlocked');
            node.innerText += ' ✓';
        } else {
            node.classList.remove('locked');
            node.classList.add('available');
        }

        node.addEventListener('click', () => unlockSkill(skillName, node));
    });
});

/* ================= UNLOCK ================= */
function unlockSkill(skill, node) {
    node.classList.remove('available');
    node.classList.add('unlocked');
    node.innerText += ' ✓';

    set(ref(db, 'skills/' + skill), {
        unlocked: true
    });
}
</script>

@endsection