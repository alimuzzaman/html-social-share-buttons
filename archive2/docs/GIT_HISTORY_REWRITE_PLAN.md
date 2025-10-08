Git history rewrite plan — vendor WIP commit

Objective

Remove the large vendor WIP commit from history (or squash it) so the repository size is reduced, while preserving other commits and avoiding accidental data loss.

Options

1. Revert the vendor commit (safe, non-destructive)
   - Use `git revert <commit-hash>` to create a new commit that removes the files added by the vendor commit. This preserves history and is the safest option.
   - Pros: reversible, preserves commit history, safest for shared repos.
   - Cons: repository size is not reduced in the packfile until garbage collection is performed; vendor files still present in earlier commits and overall repo size remains larger until a GC is run server-side.

2. Interactive rebase to squash/remove the vendor commit (history rewrite, destructive)
   - Use `git rebase -i <base-commit>` to remove or squash the vendor commit into previous commits.
   - Pros: removes vendor commit from history and reduces repo size for future clones.
   - Cons: rewrites history. If the branch is shared or pushed, it requires force-pushing and coordination with collaborators.

3. Filter-branch or git filter-repo (rewrite across entire history)
   - Use `git filter-repo` (preferred over `filter-branch`) to remove vendor/ from all commits: `git filter-repo --path vendor/ --invert-paths`.
   - Pros: cleans entire repository history, removing vendor files from all commits.
   - Cons: destructive, requires force-push and all collaborators must re-clone or rebase; may affect tags and PRs.

Recommended safe approach (step-by-step)

1. Preferred: Revert the vendor commit
   - Identify vendor commit hash (example: `331bd09`).
   - Run: `git revert --no-edit 331bd09`
   - This will create a new commit that removes the vendor files while preserving history.
   - Push the revert commit: `git push origin new`.

2. If you *must* remove vendor files from history (and are comfortable with history rewrite):
   - Coordinate with all contributors to ensure no-one has work based on the branch.
   - Create a backup branch pointing to the current state: `git branch backup-with-vendor`.
   - Ensure you do a local clone of the repository for safety.
   - Use `git filter-repo` (install separately):
     - `git filter-repo --path vendor/ --invert-paths`
   - Force-push the cleaned branch: `git push --force origin new`.
   - Instruct collaborators to re-clone or run commands to realign their local clones.

Precautions & Rollback

- Always create a backup branch/tag before rewriting history.
- Notify collaborators and set a maintenance window.
- Use `git reflog` to recover if needed before garbage collection.
- If remote hosting (GitHub/GitLab) runs periodic GC, the size reduction will be evident after garbage collection.

Approval

Do not run any history rewrite until you explicitly confirm my plan and give explicit permission to proceed. I will prepare exact commands and assistance for executing the rewrite when you approve.
