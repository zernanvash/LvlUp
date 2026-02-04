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
<style>
/* ===== HEX CORE ===== */
.hex {
    clip-path: polygon(
        25% 0%, 75% 0%,
        100% 50%,
        75% 100%, 25% 100%,
        0% 50%
    );
}

/* ===== NODES ===== */
.hex-node {
    width: 96px;
    height: 110px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    transition: all .3s ease;
}

.hex-node:hover {
    transform: translateY(-4px) scale(1.05);
}

/* STATES */
.locked {
    background: #30363d;
    color: #8b949e;
}

.available {
    background: rgba(99,102,241,.15);
    border: 1px solid #6366f1;
    color: #c7d2fe;
    box-shadow: 0 0 12px rgba(99,102,241,.4);
    cursor: pointer;
}

.unlocked {
    background: #22c55e;
    color: #052e16;
    box-shadow: 0 0 18px rgba(34,197,94,.6);
}

/* CENTER NODE */
.core-glow {
    box-shadow: 0 0 30px rgba(99,102,241,.8);
}
</style>

<div class="relative w-full min-h-[calc(100vh-64px)] bg-[#0d1117] flex flex-col items-center pt-20">

    <!-- GOAL BAR -->
    <div class="sticky top-4 z-30 w-full max-w-3xl mb-16">
        <div class="bg-[#161b22]/70 backdrop-blur-lg border border-[#30363d] rounded-xl p-4 flex items-center gap-6 shadow-2xl">
            <div class="bg-indigo-500/20 p-3 rounded-lg border border-indigo-500/30">
                <i class="fa-solid fa-microchip text-indigo-400"></i>
            </div>
            <div class="flex-1">
                <div class="flex justify-between items-end mb-2">
                    <span class="text-xs font-bold text-[#8b949e]">CURRENT GOAL: HEX-MASTER</span>
                    <span class="text-[10px] font-mono text-indigo-400">85% COMPLETE</span>
                </div>
                <div class="h-1.5 w-full bg-[#30363d] rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-500 shadow-[0_0_10px_#6366f1]" style="width:85%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- SKILL TREE -->
    <div class="relative scale-110">

        <!-- GRID -->
        <div class="grid grid-cols-5 gap-x-8 gap-y-4 place-items-center">

            <!-- ROW 1 -->
            <div></div>
            <div class="hex hex-node locked">CLOUD</div>
            <div></div>
            <div class="hex hex-node locked">DATABASE</div>
            <div></div>

            <!-- ROW 2 -->
            <div class="hex hex-node available" data-skill="api">API</div>
            <div></div>
            <div class="hex hex-node unlocked" data-skill="frontend">FRONTEND</div>
            <div></div>
            <div class="hex hex-node locked">BACKEND</div>

            <!-- ROW 3 (CENTER) -->
            <div></div>
            <div class="hex hex-node locked">SECURITY</div>

            <!-- USER CORE -->
            <div class="relative z-10">
                <div class="hex w-36 h-40 bg-indigo-500 p-1 core-glow">
                    <div class="hex w-full h-full bg-[#0d1117] flex items-center justify-center">
                        <span class="text-4xl font-black text-indigo-400">US</span>
                    </div>
                </div>
                <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 bg-indigo-500 text-[11px] font-black px-3 py-1 rounded-full shadow">
                    ROOT
                </div>
            </div>

            <div class="hex hex-node locked">DEVOPS</div>
            <div></div>

            <!-- ROW 4 -->
            <div class="hex hex-node unlocked" data-skill="git">GIT</div>
            <div></div> 
            <div class="hex hex-node available" data-skill="linux">LINUX</div>
            <div></div>
            <div class="hex hex-node locked">NETWORK</div>

        </div>
    </div>

    <!-- BACKGROUND ICON -->
    <div class="fixed bottom-0 right-0 p-10 opacity-10 pointer-events-none">
        <i class="fa-solid fa-hexagon-nodes text-[18rem]"></i>
    </div>
</div>

<!-- ===== FIREBASE ===== -->
<script type="module">
/* ================= FIREBASE IMPORTS ================= */
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-app.js";
import { getDatabase, ref, get, set } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-database.js";

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
