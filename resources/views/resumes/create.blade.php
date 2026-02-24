<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Resume') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-6">AI-Powered Resume Builder</h3>

                    <form id="resumeForm" method="POST" action="{{ route('resumes.store') }}" x-data="resumeBuilder()">
                        @csrf

                        <!-- Step 1: Job Information -->
                        <div class="mb-8">
                            <h4 class="text-md font-semibold mb-4 text-purple-600">Step 1: Job Information</h4>
                            
                            <div class="mb-4">
                                <label for="job_title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Job Title <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="job_title" 
                                       id="job_title" 
                                       x-model="jobTitle"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                       placeholder="e.g., Senior Full Stack Developer"
                                       required>
                            </div>

                            <div class="mb-4">
                                <label for="job_description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Job Description <span class="text-red-500">*</span>
                                </label>
                                <textarea name="job_description" 
                                          id="job_description" 
                                          x-model="jobDescription"
                                          rows="8" 
                                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                          placeholder="Paste the complete job description here. Include required skills, responsibilities, and qualifications..."
                                          required></textarea>
                                <p class="text-xs text-gray-500 mt-1">
                                    Tip: Include the full job posting for better matching results
                                </p>
                            </div>

                            <div>
                                <button type="button" 
                                        @click="analyzeJob()"
                                        :disabled="analyzing || !jobTitle || !jobDescription"
                                        class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                                    <span x-show="!analyzing" class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                        Analyze & Match Projects
                                    </span>
                                    <span x-show="analyzing" class="flex items-center gap-2">
                                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Analyzing...
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Project Selection -->
                        <div x-show="analyzed" x-transition class="mb-8">
                            <h4 class="text-md font-semibold mb-4 text-purple-600">Step 2: Select & Order Projects</h4>
                            
                            <!-- Match Score Display -->
                            <div class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg p-4 mb-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600 mb-1">Overall Match Score</p>
                                        <p class="text-3xl font-bold text-purple-600" x-text="matchScore + '%'"></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600 mb-1">Extracted Keywords</p>
                                        <p class="text-lg font-semibold text-blue-600" x-text="keywords.length"></p>
                                    </div>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2" x-show="keywords.length > 0">
                                    <template x-for="keyword in keywords.slice(0, 10)" :key="keyword">
                                        <span class="bg-white text-purple-700 px-2 py-1 rounded text-xs font-medium border border-purple-200" x-text="keyword"></span>
                                    </template>
                                    <span x-show="keywords.length > 10" class="text-xs text-gray-500 self-center">
                                        + <span x-text="keywords.length - 10"></span> more
                                    </span>
                                </div>
                            </div>

                            <!-- Project Selection Grid -->
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-3">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-semibold" x-text="selectedProjects.length"></span> projects selected
                                    </p>
                                    <button type="button" 
                                            @click="selectTopN(5)"
                                            class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                                        Select Top 5
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 gap-3">
                                    <template x-for="(project, index) in projects" :key="project.id">
                                        <div class="border rounded-lg p-4 transition-all"
                                             :class="selectedProjects.includes(project.id) ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-gray-300'">
                                            <label class="flex items-start cursor-pointer">
                                                <input type="checkbox" 
                                                       name="selected_project_ids[]" 
                                                       :value="project.id"
                                                       x-model="selectedProjects"
                                                       class="mt-1 mr-3 text-purple-600 focus:ring-purple-500">
                                                <div class="flex-1">
                                                    <div class="flex items-start justify-between">
                                                        <div class="flex-1">
                                                            <div class="font-semibold text-gray-900" x-text="project.name"></div>
                                                            <div class="text-sm text-gray-600 mt-1 line-clamp-2" x-text="project.description"></div>
                                                        </div>
                                                        <!-- Relevance Score Badge -->
                                                        <div class="ml-4 flex-shrink-0">
                                                            <div class="flex items-center gap-2">
                                                                <div class="text-right">
                                                                    <div class="text-xs text-gray-500">Relevance</div>
                                                                    <div class="text-lg font-bold"
                                                                         :class="{
                                                                             'text-green-600': project.relevance_score >= 70,
                                                                             'text-blue-600': project.relevance_score >= 40 && project.relevance_score < 70,
                                                                             'text-gray-600': project.relevance_score < 40
                                                                         }"
                                                                         x-text="project.relevance_score + '%'">
                                                                    </div>
                                                                </div>
                                                                <!-- Relevance Indicator -->
                                                                <div class="w-2 h-16 rounded-full"
                                                                     :class="{
                                                                         'bg-green-500': project.relevance_score >= 70,
                                                                         'bg-blue-500': project.relevance_score >= 40 && project.relevance_score < 70,
                                                                         'bg-gray-400': project.relevance_score < 40
                                                                     }">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Skills -->
                                                    <div class="flex flex-wrap gap-1 mt-2" x-show="project.skills && project.skills.length > 0">
                                                        <template x-for="skill in project.skills" :key="skill">
                                                            <span class="bg-gray-200 text-gray-700 px-2 py-0.5 rounded text-xs" x-text="skill"></span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Reordering Instructions -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                                <p class="text-sm text-blue-800">
                                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    Projects are automatically ordered by relevance. You can manually reorder them after saving by editing the resume.
                                </p>
                            </div>
                        </div>

                        <!-- Step 3: Template Selection -->
                        <div x-show="analyzed" x-transition class="mb-8">
                            <h4 class="text-md font-semibold mb-4 text-purple-600">Step 3: Choose Template</h4>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <label class="cursor-pointer">
                                    <input type="radio" name="template" value="modern" x-model="selectedTemplate" class="sr-only peer">
                                    <div class="border-2 rounded-lg p-4 text-center transition-all peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-gray-400">
                                        <div class="w-full h-32 bg-gradient-to-br from-blue-100 to-blue-200 rounded mb-2 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <p class="font-semibold">Modern</p>
                                        <p class="text-xs text-gray-500 mt-1">Clean & Professional</p>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="template" value="classic" x-model="selectedTemplate" class="sr-only peer">
                                    <div class="border-2 rounded-lg p-4 text-center transition-all peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-gray-400">
                                        <div class="w-full h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded mb-2 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                        </div>
                                        <p class="font-semibold">Classic</p>
                                        <p class="text-xs text-gray-500 mt-1">Traditional & Formal</p>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="template" value="minimal" x-model="selectedTemplate" class="sr-only peer">
                                    <div class="border-2 rounded-lg p-4 text-center transition-all peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-gray-400">
                                        <div class="w-full h-32 bg-gradient-to-br from-slate-100 to-slate-200 rounded mb-2 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                            </svg>
                                        </div>
                                        <p class="font-semibold">Minimal</p>
                                        <p class="text-xs text-gray-500 mt-1">Simple & Elegant</p>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="template" value="creative" x-model="selectedTemplate" class="sr-only peer">
                                    <div class="border-2 rounded-lg p-4 text-center transition-all peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-gray-400">
                                        <div class="w-full h-32 bg-gradient-to-br from-purple-100 to-pink-200 rounded mb-2 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                            </svg>
                                        </div>
                                        <p class="font-semibold">Creative</p>
                                        <p class="text-xs text-gray-500 mt-1">Bold & Colorful</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex gap-4 pt-4 border-t">
                            <button type="submit" 
                                    :disabled="!analyzed || selectedProjects.length === 0"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Save Resume Configuration
                                </span>
                            </button>
                            <a href="{{ route('resumes.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-all">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function resumeBuilder() {
            return {
                jobTitle: '',
                jobDescription: '',
                analyzing: false,
                analyzed: false,
                projects: [],
                selectedProjects: [],
                matchScore: 0,
                keywords: [],
                selectedTemplate: 'modern',

                async analyzeJob() {
                    if (!this.jobDescription || !this.jobTitle) {
                        alert('Please enter both job title and description');
                        return;
                    }

                    this.analyzing = true;

                    try {
                        const response = await fetch('{{ route('resumes.analyze') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                job_description: this.jobDescription
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.projects = data.projects;
                            this.matchScore = Math.round(data.match_score);
                            this.keywords = data.keywords || [];
                            this.analyzed = true;
                            
                            // Auto-select top 5 projects
                            this.selectTopN(5);
                            
                            // Scroll to results
                            setTimeout(() => {
                                document.querySelector('[x-show="analyzed"]')?.scrollIntoView({ 
                                    behavior: 'smooth', 
                                    block: 'start' 
                                });
                            }, 100);
                        }
                    } catch (error) {
                        console.error('Error analyzing job:', error);
                        alert('Failed to analyze job description. Please try again.');
                    } finally {
                        this.analyzing = false;
                    }
                },

                selectTopN(n) {
                    this.selectedProjects = this.projects.slice(0, n).map(p => p.id);
                }
            }
        }
    </script>
</x-app-layout>
