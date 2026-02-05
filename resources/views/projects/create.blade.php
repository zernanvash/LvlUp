@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-6">
    <h1 class="text-2xl font-bold text-white mb-6">Initialize New Project</h1>

    <form action="/projects" method="POST" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-[#8b949e] mb-2">Project Title</label>
                <input type="text" name="name" required class="w-full bg-[#0d1117] border border-[#30363d] rounded-md px-4 py-2 text-white focus:border-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#8b949e] mb-2">Primary Language</label>
                <select name="language" class="w-full bg-[#0d1117] border border-[#30363d] rounded-md px-4 py-2 text-white outline-none">
                    <option value="PHP">PHP</option>
                    <option value="JavaScript">JavaScript</option>
                    <option value="Python">Python</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-[#8b949e] mb-2">Paste Code or File Content</label>
            <textarea name="content" id="code_input" rows="10" class="w-full font-mono text-xs bg-[#010409] border border-[#30363d] rounded-md px-4 py-2 text-indigo-300 outline-none" placeholder="Paste code here..."></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-[#8b949e] mb-2">Tags (comma separated)</label>
            <input type="text" name="tags" id="tag_input" class="w-full bg-[#0d1117] border border-[#30363d] rounded-md px-4 py-2 text-white outline-none">
            <div id="suggestions" class="flex gap-2 mt-3"></div>
        </div>

        <button type="submit" class="bg-[#238636] hover:bg-[#2ea043] text-white px-6 py-2 rounded-md font-bold transition">Deploy Project</button>
    </form>
</div>

<script>
    const codeArea = document.getElementById('code_input');
    const tagInput = document.getElementById('tag_input');
    const suggestDiv = document.getElementById('suggestions');

    // Simple dictionary for analysis
    const library = {
        'Eloquent': 'Database',
        'Route::': 'Backend',
        'addEventListener': 'Frontend',
        'tailwind': 'CSS',
        'import React': 'React'
    };

    codeArea.addEventListener('input', () => {
        const val = codeArea.value;
        suggestDiv.innerHTML = '';
        
        Object.keys(library).forEach(key => {
            if (val.includes(key) && !tagInput.value.includes(library[key])) {
                let btn = document.createElement('button');
                btn.type = 'button';
                btn.className = "text-[10px] bg-indigo-500/20 text-indigo-400 border border-indigo-500/50 px-2 py-1 rounded";
                btn.innerText = "+ " + library[key];
                btn.onclick = () => {
                    tagInput.value += (tagInput.value ? ', ' : '') + library[library[key] ? key : key]; // Simplified logic
                    tagInput.value = [...new Set(tagInput.value.split(', '))].join(', '); // unique tags
                };
                suggestDiv.appendChild(btn);
            }
        });
    });
</script>
@endsection