---
paths:
  - 'resources/views/pages/**'
---

# Pages

## Livewire components are single-file components (SFCs)
All Livewire components are SFCs (frontmatter anonymous class + template), never app/Livewire classes. Routed pages live in resources/views/pages (pages:: namespace); page-scoped child components colocate next to their parent page (e.g. pages/settings/create-membership-form); promote a component to resources/views/components only when it gains a second consumer. Filenames keep the ⚡ prefix (make_command.emoji is true).

## SFC frontmatter style follows the make:livewire stub
Format frontmatter like the livewire-sfc stub: opening brace of the anonymous class on its own line, then `};` and `?>` on separate lines. Declare every import in the frontmatter use-block (sorted); never use @use() directives in an SFC template — frontmatter and template compile into one PHP file, so @use duplicates are fatal.

## Set localized page titles via the rendering() hook
SFCs cannot override render(). For static titles use #[Title('...')]; for localized/dynamic titles define `public function rendering(View $view): void { $view->title(__('...')); }`. Settings pages title as `__('navigation.settings.x').' - '.__('settings.title')`.
