# DESIGN SYSTEM

## 1. MARCA

```
Nome: Consultoria SaaS
Tagline: A consultoria especializada que sua empresa merece
Tipo: Premium · Profissional · Tecnológico
```

## 2. PALETA DE CORES

### Light Mode

| Token | Cor | Hex | Uso |
|-------|-----|-----|-----|
| primary | Blue 600 | #2563EB | Botões primários, links, destaques |
| primary-dark | Blue 700 | #1D4ED8 | Hover |
| primary-light | Blue 50 | #EFF6FF | Backgrounds sutis |
| secondary | Slate 600 | #475569 | Textos secundários |
| accent | Emerald 500 | #10B981 | Sucesso, cashback, badges |
| warning | Amber 500 | #F59E0B | Alertas |
| danger | Red 500 | #EF4444 | Erros, cancelamentos |
| surface | White | #FFFFFF | Cards, modais |
| background | Gray 50 | #F8FAFC | Background principal |
| text-primary | Slate 900 | #0F172A | Títulos |
| text-secondary | Slate 500 | #64748B | Corpo |
| border | Gray 200 | #E2E8F0 | Bordas |
| muted | Gray 100 | #F1F5F9 | Backgrounds secundários |

### Dark Mode

| Token | Cor | Hex |
|-------|-----|-----|
| surface | Slate 800 | #1E293B |
| background | Slate 900 | #0F172A |
| text-primary | Gray 50 | #F8FAFC |
| text-secondary | Slate 400 | #94A3B8 |
| border | Slate 700 | #334155 |
| muted | Slate 800 | #1E293B |

## 3. TIPOGRAFIA

| Elemento | Fonte | Peso | Tamanho | Altura |
|----------|-------|------|---------|--------|
| Display | Inter | Bold | 48px | 1.2 |
| H1 | Inter | Bold | 36px | 1.25 |
| H2 | Inter | SemiBold | 28px | 1.3 |
| H3 | Inter | SemiBold | 22px | 1.35 |
| Body LG | Inter | Regular | 16px | 1.5 |
| Body SM | Inter | Regular | 14px | 1.5 |
| Caption | Inter | Regular | 12px | 1.4 |
| Label | Inter | Medium | 14px | 1.4 |
| Button | Inter | SemiBold | 15px | 1.4 |

## 4. ESPAÇAMENTOS

| Token | px | rem |
|-------|-----|-----|
| xs | 4px | 0.25rem |
| sm | 8px | 0.5rem |
| md | 16px | 1rem |
| lg | 24px | 1.5rem |
| xl | 32px | 2rem |
| 2xl | 48px | 3rem |
| 3xl | 64px | 4rem |

## 5. BORDAS E SOMBRAS

| Token | Valor |
|-------|-------|
| radius-sm | 6px |
| radius-md | 10px |
| radius-lg | 16px |
| radius-xl | 24px |
| radius-full | 9999px |
| shadow-sm | 0 1px 2px rgba(0,0,0,0.05) |
| shadow-md | 0 4px 6px rgba(0,0,0,0.07) |
| shadow-lg | 0 10px 25px rgba(0,0,0,0.1) |

## 6. COMPONENTES

### Botões

```
Primary   [█████████████]
Secondary [█████████████]
Outline   [█████████████]
Ghost     [█████████████]
Danger    [█████████████]
Sizes: sm [█], md [███], lg [█████]
```

### Inputs

```
Label
┌──────────────────────────────┐
│ Placeholder                   │
└──────────────────────────────┘
Helper text

Erro:
┌──────────────────────────────┐
│ Valor inválido               │
└──────────────────────────────┘
❌ Mensagem de erro
```

### Cards

```
┌─────────────────────────────────────┐
│                                     │
│  Título do Card                     │
│  Descrição ou conteúdo              │
│                                     │
│  [Ação]                             │
└─────────────────────────────────────┘
```

### Tabelas

```
┌─────────┬──────────┬──────────┬──────────┐
│ Header  │ Header   │ Header   │ Header   │
├─────────┼──────────┼──────────┼──────────┤
│ Dado    │ Dado     │ Dado     │ Dado     │
│ Dado    │ Dado     │ Dado     │ Dado     │
└─────────┴──────────┴──────────┴──────────┘
```

### Badges / Tags

```
[Sucesso]  [Atenção]  [Erro]  [Info]
[Premium]  [Novo]     [Beta]  [Em Breve]
```

## 7. GRID

```
Desktop: 12 colunas, gutter 24px, max-width 1280px
Tablet:  12 colunas, gutter 16px, max-width 1024px
Mobile:  4 colunas, gutter 16px, max-width 640px
```

## 8. ANIMAÇÕES

| Tipo | Duração | Easing | Uso |
|------|---------|--------|-----|
| Fade | 200ms | ease | Aparecer/desaparecer |
| Slide | 300ms | ease-out | Drawers, modais |
| Scale | 150ms | ease-in-out | Hover em cards |
| Spin | 1s | linear | Loading |

## 9. ÍCONES

Lucide Icons (conjunto compatível com o design minimalista).

## 10. TOKENS DE EXEMPLO (CSS Custom Properties)

```css
:root {
  --color-primary: #2563EB;
  --color-primary-dark: #1D4ED8;
  --color-primary-light: #EFF6FF;
  --color-surface: #FFFFFF;
  --color-background: #F8FAFC;
  --color-text-primary: #0F172A;
  --color-text-secondary: #64748B;
  --color-border: #E2E8F0;
  --color-success: #10B981;
  --color-warning: #F59E0B;
  --color-danger: #EF4444;

  --font-family: 'Inter', sans-serif;
  --font-display: 700 48px/1.2 var(--font-family);
  --font-h1: 700 36px/1.25 var(--font-family);
  --font-h2: 600 28px/1.3 var(--font-family);
  --font-h3: 600 22px/1.35 var(--font-family);
  --font-body: 400 16px/1.5 var(--font-family);
  --font-body-sm: 400 14px/1.5 var(--font-family);
  --font-label: 500 14px/1.4 var(--font-family);
  --font-button: 600 15px/1.4 var(--font-family);

  --space-xs: 4px;
  --space-sm: 8px;
  --space-md: 16px;
  --space-lg: 24px;
  --space-xl: 32px;
  --space-2xl: 48px;
  --space-3xl: 64px;

  --radius-sm: 6px;
  --radius-md: 10px;
  --radius-lg: 16px;
  --radius-xl: 24px;

  --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
  --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
  --shadow-lg: 0 10px 25px rgba(0,0,0,0.1);
}

[data-theme="dark"] {
  --color-surface: #1E293B;
  --color-background: #0F172A;
  --color-text-primary: #F8FAFC;
  --color-text-secondary: #94A3B8;
  --color-border: #334155;
}
```
