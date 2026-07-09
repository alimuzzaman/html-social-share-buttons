# [SPECKIT-000] SPEC TEMPLATE (PRD + Speckit Hybrid)

## Metadata
- **Spec Number:** SPECKIT-000
- **Spec ID:** SPEC-YYYY-####
- **Title:** <short-title>
- **Owner:** <owner/team>
- **Status:** Draft | In Review | Approved | Done
- **Created:** <YYYY-MM-DD>
- **Last Updated:** <YYYY-MM-DD>
- **Version:** 1.0
- **Source of Truth:** Single file in `/specs/`

## 1.0 Context
### 1.1 Problem Statement
- <one paragraph>
- <business/UX/technical pain point>

### 1.2 Success Criteria
- <1-3 measurable outcomes>

## 2.0 Baseline
- <current behavior to preserve>
- <existing integrations, dependencies, constraints>
- <known debts/risks that affect this feature>

## 3.0 Goals and Non-Goals
### 3.1 Goals
1. <goal>
2. <goal>

### 3.2 Non-Goals
1. <non-goal>
2. <non-goal>

## 4.0 User Stories
- As a <persona>, I want <outcome>, so that <value>.
- As a <persona>, I want <outcome>, so that <value>.

## 5.0 Scope
### 5.1 In Scope
- <explicitly list>

### 5.2 Out of Scope
- <explicitly list>

## 6.0 Requirements
### 6.1 Functional Requirements
- REQ-001: <must do>
- REQ-002: <must do>

### 6.2 Non-Functional Requirements
- NFR-001: <performance, accessibility, compatibility, etc.>
- NFR-002: <security, reliability, etc.>

## 7.0 UX / Interaction Design
- Target pages/flows:
  - <screen or route>
- Section structure:
  - <sections and intent>
- State transitions:
  - <loading, empty, error, success>

## 8.0 Data, Settings, and Contract Surface
- Field contracts:
  - <key>: <type>, <default>, <validation>, <consumer>
- Backward compatibility:
  - <migration and compatibility notes>
- Integrations:
  - <hooks/actions/filters/APIs>

## 9.0 DB Schema Surface (if applicable)
- Table: <table-name>
- Fields:
  - <column>: <type>, <constraint>, <description>

## 10.0 Validation and Acceptance
### 10.1 Acceptance Scenarios
- **Scenario:**
  - Given: ...
  - When: ...
  - Then: ...

### 10.2 Mandatory Frontend Regression Protocol (Per Settings Change)
For every settings change, run a pre/post frontend validation pass before code finalization:
- [ ] Baseline capture: collect generated share output fixtures for all critical settings scenarios.
- [ ] Apply change and rerun fixture capture on the same environment.
- [ ] Compare normalized frontend output (HTML/CSS/JS behavior) and require explicit approval for any intentional delta.
- [ ] Record results in spec change log.

### 10.3 Manual Checklist
- [ ] <checklist item>
- [ ] <checklist item>

## 11.0 Risks and Open Questions
- Risk: <risk> — Impact: <impact> — Mitigation: <plan>
- Open Question: <question>

## 12.0 Milestones and Ownership
- Milestone 1: <output>
- Milestone 2: <output>
- Owner/Reviewer: <name/role>

## 13.0 Notes
- Add any implementation-neutral clarifications, constraints, or references.
