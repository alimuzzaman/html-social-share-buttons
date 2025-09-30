# Admin Tabs Analysis Prompt

You are reviewing the plugin's admin UI tabs. Perform the following tasks:

1. Analyse every tab in the admin interface and document:
   - The tab label.
   - The current contents/options displayed within the tab.
2. List every option discovered. Flag any duplicates and note any missing options that appear in other documentation or code.
3. Propose a reorganized tab structure. For each tab, outline which options belong there and explain the rationale.
4. Update the "Available Networks" UI concept:
   - Present the network cards in a vertical layout.
   - Enable drag-and-drop interaction across the entire card.
   - Investigate and resolve rendering issues where cards disappear when many are expanded.
   - Reduce the card height where possible while keeping readability.
   - Use the new vertical space to leverage additional horizontal layout opportunities.
5. Remove the "Network Order" option from the UI and describe any required code/database implications.

Document findings clearly so they can guide future implementation work.