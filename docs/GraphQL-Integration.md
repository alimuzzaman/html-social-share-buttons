# GraphQL Integration

This document describes the GraphQL integration for HTML Social Share Buttons plugin, which allows headless WordPress sites to access share button HTML and share counts via GraphQL queries.

## Requirements

- WordPress 5.0+
- [WPGraphQL](https://www.wpgraphql.com/) plugin (version 1.0+)
- HTML Social Share Buttons plugin (version 3.1.0+)

## GraphQL Fields

### Post Type Fields

The following GraphQL fields are added to the `Post` and `Page` types:

#### `htmlSocialShareButtons`

Returns HTML for social share buttons.

**Type:** `String`

**Arguments:**
- `profile` (String): Optional profile name to use for rendering
- `networks` ([String]): Optional array of specific networks to include
- `showCounts` (Boolean): Whether to show share counts (future feature)

**Example Query:**

```graphql
{
  posts {
    nodes {
      id
      title
      htmlSocialShareButtons
    }
  }
}
```

**Example with Arguments:**

```graphql
{
  posts {
    nodes {
      id
      title
      htmlSocialShareButtons(
        networks: ["facebook", "twitter"]
        showCounts: true
      )
    }
  }
}
```

#### `htmlSocialShareCounts`

Returns share counts as JSON-encoded string.

**Type:** `String`

**Example Query:**
```graphql
{
  posts {
    nodes {
      id
      title
      htmlSocialShareCounts
    }
  }
}
```

## Usage in Headless Applications

### React/Next.js Example

```javascript
import { gql, useQuery } from '@apollo/client';

const GET_POST_WITH_SHARE_BUTTONS = gql`
  query GetPost($id: ID!) {
    post(id: $id, idType: DATABASE_ID) {
      id
      title
      content
      htmlSocialShareButtons(networks: ["facebook", "twitter", "linkedin"])
    }
  }
`;

function Post({ postId }) {
  const { loading, error, data } = useQuery(GET_POST_WITH_SHARE_BUTTONS, {
    variables: { id: postId }
  });

  if (loading) return <p>Loading...</p>;
  if (error) return <p>Error: {error.message}</p>;

  return (
    <article>
      <h1>{data.post.title}</h1>
      <div dangerouslySetInnerHTML={{ __html: data.post.content }} />
      <div dangerouslySetInnerHTML={{ __html: data.post.htmlSocialShareButtons }} />
    </article>
  );
}
```

### Gatsby Example

```javascript
import React from 'react';
import { graphql } from 'gatsby';

export const query = graphql`
  query PostQuery($id: String!) {
    wpPost(id: { eq: $id }) {
      id
      title
      content
      htmlSocialShareButtons
    }
  }
`;

const PostTemplate = ({ data }) => {
  const post = data.wpPost;

  return (
    <article>
      <h1>{post.title}</h1>
      <div dangerouslySetInnerHTML={{ __html: post.content }} />
      <div dangerouslySetInnerHTML={{ __html: post.htmlSocialShareButtons }} />
    </article>
  );
};

export default PostTemplate;
```

## Configuration

The GraphQL fields respect the plugin's settings:

- **Enabled Networks:** Only networks enabled in plugin settings are included
- **Profiles:** Custom profiles can be specified via the `profile` argument
- **Styling:** The generated HTML includes the plugin's CSS classes for styling

## Security Considerations

- HTML output is properly escaped and sanitized
- Only enabled networks are rendered
- Invalid arguments are handled gracefully
- Errors are logged but not exposed in GraphQL responses

## Future Enhancements

- GraphQL mutations for updating share counts
- More granular control over button styling
- Support for custom share URLs and titles
- Integration with WPGraphQL's connection system
