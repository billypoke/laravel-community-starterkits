import { ref } from 'vue';

export function useGithubValidation() {
  const isValidating = ref(false);
  const validationError = ref('');

  const validateGithubUrl = async (url: string): Promise<boolean> => {
    try {
      isValidating.value = true;
      validationError.value = '';

      // First, validate that the URL is a GitHub URL
      if (!url.startsWith('https://github.com/')) {
        validationError.value = 'URL must be a valid GitHub repository URL';
        return false;
      }

      // Make a request to the GitHub API to check if the repository exists
      // We'll use the repos endpoint with the username/repository format
      const parts = url.replace('https://github.com/', '').split('/');
      if (parts.length < 2) {
        validationError.value = 'Invalid repository path';
        return false;
      }

      const username = parts[0];
      const repo = parts[1];

      // Using fetch to check if the repository exists
      const response = await fetch(`https://api.github.com/repos/${username}/${repo}`);

      if (!response.ok) {
        if (response.status === 404) {
          validationError.value = 'Repository not found. Please check the URL and try again.';
        } else {
          validationError.value = `GitHub API error: ${response.statusText}`;
        }
        return false;
      }

      return true;
    } catch (error) {
      validationError.value = `Error validating repository: ${error instanceof Error ? error.message : String(error)}`;
      return false;
    } finally {
      isValidating.value = false;
    }
  };

  return {
    isValidating,
    validationError,
    validateGithubUrl
  };
}
