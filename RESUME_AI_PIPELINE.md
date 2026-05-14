# Resume AI PDF Generation Pipeline

This document defines the recommended AI pipeline for generating high-quality, ATS-friendly resume PDFs in the Laravel resume builder.

## Goal

Generate a professional resume PDF by separating the work into three stages:

1. Resume content generation
2. Resume template/layout generation
3. PDF rendering

---

## Recommended Models

### 1. Main Resume Content Model

**Model:** `mistral-medium-3.5-128b`

**Purpose:** Generate the actual resume content.

Use this model for:

- ATS-friendly resume summaries
- Project descriptions
- Skill organization
- Experience bullet points
- Education descriptions
- Resume-job matching
- Structured JSON output

NVIDIA lists this model as strong for text generation, coding, and agentic use cases, which makes it a good main model for resume generation. :contentReference[oaicite:0]{index=0}

---

### 2. Resume Template / Layout Model

**Model:** `mistral-small-4-119b-2603`

**Purpose:** Generate or improve the resume template code.

Use this model for:

- Laravel Blade resume templates
- DomPDF-compatible HTML/CSS
- Resume layout fixes
- Page-break handling
- Spacing and typography improvements
- Template variations such as modern, classic, compact, or creative

This model is useful for code generation and has a large context window, which helps when working with long Blade files or CSS-heavy templates. :contentReference[oaicite:1]{index=1}

---

### 3. Long Context / Resume Matching Model

**Model:** `nemotron-3-super-120b-a12b`

**Purpose:** Analyze large profile data and job descriptions.

Use this model when the input is very long, such as:

- Full user profile
- Many projects
- Many skills
- Certificates
- Job description
- Existing resume draft
- Portfolio content

This model supports very large context and is useful for reasoning, planning, coding, and tool-calling workflows. :contentReference[oaicite:2]{index=2}

---

## Pipeline Overview

```txt
User Profile Data
        |
        v
mistral-medium-3.5-128b
        |
        v
ATS-Friendly Resume JSON
        |
        v
Laravel Blade Template
        |
        v
mistral-small-4-119b-2603
        |
        v
Improved HTML/CSS Resume Layout
        |
        v
DomPDF or Puppeteer
        |
        v
Final Resume PDF