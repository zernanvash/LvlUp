<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Collection;
use App\Services\AiResumeWriter;
class ResumeAnalyzer
{
    /**
     * Common stop words to exclude from keyword extraction
     */
    private const STOP_WORDS = [
        'a', 'an', 'and', 'are', 'as', 'at', 'be', 'by', 'for', 'from',
        'has', 'he', 'in', 'is', 'it', 'its', 'of', 'on', 'that', 'the',
        'to', 'was', 'will', 'with', 'we', 'you', 'your', 'our', 'their',
        'this', 'these', 'those', 'have', 'had', 'been', 'can', 'could',
        'should', 'would', 'may', 'might', 'must', 'shall', 'or', 'but',
        'not', 'no', 'yes', 'if', 'then', 'than', 'so', 'such', 'very',
        'just', 'about', 'into', 'through', 'during', 'before', 'after',
        'above', 'below', 'up', 'down', 'out', 'off', 'over', 'under',
        'again', 'further', 'once', 'here', 'there', 'when', 'where',
        'why', 'how', 'all', 'both', 'each', 'few', 'more', 'most',
        'other', 'some', 'any', 'only', 'own', 'same', 'too', 'also'
    ];

    /**
     * Common technology keywords to prioritize
     */
private const TECH_KEYWORDS = [
        // Languages
        'php', 'laravel', 'javascript', 'typescript', 'python', 'ruby', 'go', 'golang',
        'rust', 'java', 'kotlin', 'swift', 'dart', 'c#', 'dotnet', '.net', 'asp.net',
        'elixir', 'scala', 'r', 'perl', 'lua', 'haskell', 'clojure', 'erlang',

        // Frontend
        'react', 'vue', 'angular', 'svelte', 'nextjs', 'next.js', 'nuxtjs', 'nuxt',
        'remix', 'astro', 'html', 'css', 'sass', 'tailwind', 'bootstrap', 'shadcn',
        'radix', 'webpack', 'vite', 'parcel', 'turbopack',

        // Backend / Frameworks
        'node', 'nodejs', 'express', 'fastapi', 'django', 'flask', 'rails', 'spring',
        'nestjs', 'hono', 'fiber', 'gin', 'actix',

        // Mobile
        'flutter', 'react native', 'ios', 'android', 'expo',

        // Data / Databases
        'sql', 'mysql', 'postgresql', 'sqlite', 'mongodb', 'redis', 'elasticsearch',
        'dynamodb', 'cassandra', 'neo4j', 'supabase', 'prisma', 'drizzle',

        // Cloud / DevOps
        'docker', 'kubernetes', 'k8s', 'terraform', 'ansible', 'pulumi', 'helm',
        'aws', 'azure', 'gcp', 'cloud', 'devops', 'ci/cd', 'github actions',
        'jenkins', 'circleci', 'linux', 'nginx', 'caddy', 'cloudflare',

        // AI / ML
        'machine learning', 'deep learning', 'ai', 'llm', 'langchain', 'openai',
        'gemini', 'tensorflow', 'pytorch', 'scikit-learn', 'huggingface', 'rag',
        'vector database', 'embeddings', 'fine-tuning', 'data science',

        // APIs / Architecture
        'api', 'rest', 'graphql', 'grpc', 'websockets', 'microservices', 'serverless',
        'event-driven', 'message queue', 'kafka', 'rabbitmq', 'pubsub',

        // Security / Other
        'git', 'agile', 'scrum', 'tdd', 'testing', 'jest', 'vitest', 'pytest',
        'phpunit', 'selenium', 'playwright', 'cypress', 'security', 'cybersecurity',
        'oauth', 'jwt', 'web3', 'blockchain', 'solidity',

        // Tooling
        'sdk', 'cli', 'api', 'ui', 'ux', 'xml', 'json', 'yaml', 'graphql',
    ];

    /**
     * Extract technology keywords from a job description
     *
     * @param string $jobDescription The job description text
     * @return array Array of extracted keywords
     */
    public function extractKeywords(string $jobDescription): array
    {
        // Convert to lowercase for case-insensitive matching
        $text = strtolower($jobDescription);
        
        // Remove special characters but keep spaces and hyphens
        $text = preg_replace('/[^a-z0-9\s\-\.#\+]/', ' ', $text);
        
        // Tokenize into words
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        $keywords = [];
        
        // Extract multi-word tech terms first (e.g., "machine learning", "asp.net")
        foreach (self::TECH_KEYWORDS as $techTerm) {
            if (str_contains($text, $techTerm)) {
                $keywords[] = $techTerm;
            }
        }
        
        // Extract single-word keywords
        foreach ($words as $word) {
            // Skip stop words
            if (in_array($word, self::STOP_WORDS)) {
                continue;
            }
            
            // Skip very short words (less than 2 characters)
            if (strlen($word) < 2) {
                continue;
            }
            
            // Skip numbers only
            if (is_numeric($word)) {
                continue;
            }
            
            // Add word if it looks like a technology term
            if ($this->isTechnologyTerm($word)) {
                $keywords[] = $word;
            }
        }
        
        // Remove duplicates and return
        return array_values(array_unique($keywords));
    }

    /**
     * Score a project against extracted keywords
     *
     * @param Project $project The project to score
     * @param array $keywords Array of keywords to match against
     * @return float Score between 0 and 100
     */
    public function scoreProject(Project $project, array $keywords): float
    {
        if (empty($keywords)) {
            return 0.0;
        }
        
        $score = 0.0;
        $maxPossibleScore = 0.0;
        
        // Prepare project text fields for matching
        $projectName = strtolower($project->name ?? '');
        $projectDescription = strtolower($project->description ?? '');
        $projectLanguage = strtolower($project->language ?? '');
        
        // Get skill tags
        $skillTags = $project->skills->pluck('name')->map(fn($name) => strtolower($name))->toArray();
        
        foreach ($keywords as $keyword) {
            $keyword = strtolower($keyword);
            
            // Skill tags match (weight: 3x)
            $skillMatch = false;
            foreach ($skillTags as $skillTag) {
                if ($this->fuzzyMatch($keyword, $skillTag)) {
                    $score += 3.0;
                    $skillMatch = true;
                    break;
                }
            }
            
            // Description match (weight: 2x)
            if (str_contains($projectDescription, $keyword)) {
                $score += 2.0;
            }
            
            // Name match (weight: 1x)
            if (str_contains($projectName, $keyword)) {
                $score += 1.0;
            }
            
            // Language match (weight: 3x, same as skill)
            if ($this->fuzzyMatch($keyword, $projectLanguage)) {
                $score += 3.0;
            }
            
            // Calculate max possible score (if all keywords matched in all fields)
            $maxPossibleScore += 3.0; // At least skill tag weight
        }
        
        // Normalize to 0-100 scale
        if ($maxPossibleScore > 0) {
            return min(100.0, ($score / $maxPossibleScore) * 100.0);
        }
        
        return 0.0;
    }

    /**
     * Rank projects by relevance to keywords
     *
     * @param Collection $projects Collection of projects to rank
     * @param array $keywords Array of keywords to match against
     * @return Collection Sorted collection with scores
     */
    public function rankProjects(Collection $projects, array $keywords): Collection
    {
        return $projects->map(function ($project) use ($keywords) {
            $score = $this->scoreProject($project, $keywords);
            $project->relevance_score = $score;
            return $project;
        })->sortByDesc('relevance_score')->values();
    }

    /**
     * Calculate overall match score for a user's profile
     *
     * @param User $user The user to calculate score for
     * @param array $keywords Array of keywords to match against
     * @return float Match score as percentage (0-100)
     */
    public function calculateMatchScore(User $user, array $keywords): float
    {
        if (empty($keywords)) {
            return 0.0;
        }
        
        $matchedKeywords = 0;
        
        // Get all user's skills
        $userSkills = $user->projects()
            ->with('skills')
            ->get()
            ->pluck('skills')
            ->flatten()
            ->pluck('name')
            ->map(fn($name) => strtolower($name))
            ->unique()
            ->toArray();
        
        // Get all project descriptions and names
        $projectTexts = $user->projects->map(function ($project) {
            return strtolower($project->name . ' ' . $project->description . ' ' . $project->language);
        })->implode(' ');
        
        // Check each keyword
        foreach ($keywords as $keyword) {
            $keyword = strtolower($keyword);
            $found = false;
            
            // Check in skills
            foreach ($userSkills as $skill) {
                if ($this->fuzzyMatch($keyword, $skill)) {
                    $found = true;
                    break;
                }
            }
            
            // Check in project texts
            if (!$found && str_contains($projectTexts, $keyword)) {
                $found = true;
            }
            
            if ($found) {
                $matchedKeywords++;
            }
        }
        
        // Calculate percentage
        return ($matchedKeywords / count($keywords)) * 100.0;
    }

    /**
     * Check if a word is likely a technology term
     *
     * @param string $word The word to check
     * @return bool True if likely a tech term
     */
    private function isTechnologyTerm(string $word): bool
    {
        // Check if it's in our tech keywords list
        foreach (self::TECH_KEYWORDS as $techTerm) {
            if ($word === $techTerm || str_contains($techTerm, $word)) {
                return true;
            }
        }
        
        // Check for common tech patterns
        $techPatterns = [
            '/^[a-z]+js$/',           // e.g., reactjs, vuejs
            '/^[a-z]+sql$/',          // e.g., mysql, postgresql
            '/^[a-z]+db$/',           // e.g., mongodb, dynamodb
            '/script$/',              // e.g., javascript, typescript
            '/^api$/',                // api
            '/^sdk$/',                // sdk
            '/^cli$/',                // cli
            '/^ui$/',                 // ui
            '/^ux$/',                 // ux
            '/^css$/',                // css
            '/^html$/',               // html
            '/^xml$/',                // xml
            '/^json$/',               // json
            '/^yaml$/',               // yaml
        ];
        
        foreach ($techPatterns as $pattern) {
            if (preg_match($pattern, $word)) {
                return true;
            }
        }
        
        // Check if word length suggests it might be a tech term (3-20 chars)
        return strlen($word) >= 3 && strlen($word) <= 20;
    }

    /**
     * Perform fuzzy matching between keyword and target
     *
     * @param string $keyword The keyword to match
     * @param string $target The target string to match against
     * @return bool True if there's a match
     */
    private function fuzzyMatch(string $keyword, string $target): bool
    {
        // Exact match
        if ($keyword === $target) {
            return true;
        }
        
        // Contains match
        if (str_contains($target, $keyword) || str_contains($keyword, $target)) {
            return true;
        }
        
        // Handle common variations
        $variations = [
            'javascript' => ['js', 'javascript', 'ecmascript'],
            'typescript' => ['ts', 'typescript'],
            'python' => ['py', 'python'],
            'postgresql' => ['postgres', 'postgresql', 'psql'],
            'mongodb' => ['mongo', 'mongodb'],
            'kubernetes' => ['k8s', 'kubernetes', 'kube'],
            'docker' => ['docker', 'containerization'],
            'react' => ['react', 'reactjs', 'react.js'],
            'vue' => ['vue', 'vuejs', 'vue.js'],
            'angular' => ['angular', 'angularjs'],
            'node' => ['node', 'nodejs', 'node.js'],
        ];
        
        foreach ($variations as $canonical => $aliases) {
            if (in_array($keyword, $aliases) && in_array($target, $aliases)) {
                return true;
            }
        }
        
        return false;
    }
}
