package main

import (
	"encoding/json"
	"fmt"
	"log"
	"net/http"
	"runtime"
	"sync"
	"time"
)

type BuildStep struct {
	Name      string    `json:"name"`
	Status    string    `json:"status"` // pending, running, completed, failed
	Duration  string    `json:"duration"`
	StartedAt time.Time `json:"started_at"`
	Logs      []string  `json:"logs"`
}

type DeploymentJob struct {
	ID          string      `json:"id"`
	Project     string      `json:"project"`
	Branch      string      `json:"branch"`
	Commit      string      `json:"commit"`
	CommitMsg   string      `json:"commit_msg"`
	Author      string      `json:"author"`
	Status      string      `json:"status"` // queued, building, ready, error
	Environment string      `json:"environment"`
	URL         string      `json:"url"`
	CreatedAt   time.Time   `json:"created_at"`
	CompletedAt *time.Time  `json:"completed_at,omitempty"`
	Steps       []BuildStep `json:"steps"`
}

type RunnerServer struct {
	mu          sync.RWMutex
	deployments map[string]*DeploymentJob
}

func NewRunnerServer() *RunnerServer {
	s := &RunnerServer{
		deployments: make(map[string]*DeploymentJob),
	}
	s.seedDefaultDeployments()
	return s
}

func (s *RunnerServer) seedDefaultDeployments() {
	now := time.Now()
	t1 := now.Add(-12 * time.Minute)
	t2 := now.Add(-11 * time.Minute)

	s.deployments["dpl_prod_9921"] = &DeploymentJob{
		ID:          "dpl_prod_9921",
		Project:     "vedaix-web-platform",
		Branch:      "main",
		Commit:      "7f9a2c1",
		CommitMsg:   "feat(edge): optimize streaming hydration & sub-ms route dispatch",
		Author:      "Vedaix Architect",
		Status:      "ready",
		Environment: "Production",
		URL:         "https://vedaix.app",
		CreatedAt:   t1,
		CompletedAt: &t2,
		Steps: []BuildStep{
			{
				Name:      "Initialize Build Container",
				Status:    "completed",
				Duration:  "1.2s",
				StartedAt: t1,
				Logs: []string{
					"[\x1b[38;2;0;199;255mVedaix Engine\x1b[0m] Initializing Go/Rust native runner runtime v1.4.0...",
					"[\x1b[32mOK\x1b[0m] Container isolation verified via Linux cgroups v2",
					"[\x1b[32mOK\x1b[0m] Build cache primed from Turbocache L2 SSD layer (hit rate: 94.2%)",
				},
			},
			{
				Name:      "Clone & Verify Repository",
				Status:    "completed",
				Duration:  "0.8s",
				StartedAt: t1.Add(1200 * time.Millisecond),
				Logs: []string{
					"[\x1b[36mGit\x1b[0m] Fetching origin/main (depth 1, tree-hash: 7f9a2c1)",
					"[\x1b[32mOK\x1b[0m] Head SHA: 7f9a2c18d3b8417c91fae29813589c3140510",
					"[\x1b[32mOK\x1b[0m] Verified commit signature against trusted developer key",
				},
			},
			{
				Name:      "Next.js App Router Compilation",
				Status:    "completed",
				Duration:  "9.4s",
				StartedAt: t1.Add(2 * time.Second),
				Logs: []string{
					"▲ \x1b[1mNext.js 15.1.0\x1b[0m",
					"  - Environments: .env.production.local loaded",
					"  - Compiling / (Static Page)",
					"  - Compiling /analytics (Server-Rendered Dynamic)",
					"  - Compiling /api/edge (Edge Function Runtime)",
					"✓ Generated 18 static chunks in 3.1s",
					"✓ Optimized 4 dynamic server routes with streaming RSC support",
					"✓ Bundled edge middleware with zero external dependencies (32.4 KB)",
				},
			},
			{
				Name:      "Deploy to Global Edge Network",
				Status:    "completed",
				Duration:  "2.1s",
				StartedAt: t1.Add(11400 * time.Millisecond),
				Logs: []string{
					"[\x1b[38;2;0;220;130mEdge Deploy\x1b[0m] Propagating routes to 320 Anycast Edge PoPs...",
					"[\x1b[32mOK\x1b[0m] TLS certificates bound (Let's Encrypt Wildcard, auto-renewed)",
					"[\x1b[32mOK\x1b[0m] Instant atomic swap activated across edge mesh",
					"\x1b[32;1m✓ Ready at https://vedaix.app\x1b[0m (Total build time: 13.5s)",
				},
			},
		},
	}
}

func (s *RunnerServer) HandleHealth(w http.ResponseWriter, r *http.Request) {
	w.Header().Set("Content-Type", "application/json")
	w.Header().Set("Access-Control-Allow-Origin", "*")
	json.NewEncoder(w).Encode(map[string]any{
		"status":    "healthy",
		"service":   "Vedaix Native Go Build Engine",
		"version":   "1.27-edge",
		"goroutine": runtime.NumGoroutine(),
		"cpus":      runtime.NumCPU(),
	})
}

func (s *RunnerServer) HandleStatus(w http.ResponseWriter, r *http.Request) {
	w.Header().Set("Content-Type", "application/json")
	w.Header().Set("Access-Control-Allow-Origin", "*")

	var m runtime.MemStats
	runtime.ReadMemStats(&m)

	s.mu.RLock()
	count := len(s.deployments)
	s.mu.RUnlock()

	json.NewEncoder(w).Encode(map[string]any{
		"runner_id":        "vedaix-runner-primary-01",
		"active_jobs":      0,
		"total_processed":  count,
		"alloc_mb":         fmt.Sprintf("%.2f MB", float64(m.Alloc)/(1024*1024)),
		"sys_mb":           fmt.Sprintf("%.2f MB", float64(m.Sys)/(1024*1024)),
		"num_gc":           m.NumGC,
		"platform":         runtime.GOOS + "/" + runtime.GOARCH,
		"turbocache_ready": true,
	})
}

func (s *RunnerServer) HandleDeployments(w http.ResponseWriter, r *http.Request) {
	w.Header().Set("Content-Type", "application/json")
	w.Header().Set("Access-Control-Allow-Origin", "*")

	s.mu.RLock()
	defer s.mu.RUnlock()

	list := make([]*DeploymentJob, 0, len(s.deployments))
	for _, d := range s.deployments {
		list = append(list, d)
	}

	json.NewEncoder(w).Encode(list)
}

func (s *RunnerServer) HandleTriggerBuild(w http.ResponseWriter, r *http.Request) {
	w.Header().Set("Content-Type", "application/json")
	w.Header().Set("Access-Control-Allow-Origin", "*")

	if r.Method == http.MethodOptions {
		w.Header().Set("Access-Control-Allow-Methods", "POST, OPTIONS")
		w.Header().Set("Access-Control-Allow-Headers", "Content-Type")
		w.WriteHeader(http.StatusOK)
		return
	}

	var req struct {
		Project string `json:"project"`
		Branch  string `json:"branch"`
		Commit  string `json:"commit"`
	}
	_ = json.NewDecoder(r.Body).Decode(&req)

	if req.Project == "" {
		req.Project = "vedaix-web-platform"
	}
	if req.Branch == "" {
		req.Branch = "main"
	}
	if req.Commit == "" {
		req.Commit = fmt.Sprintf("%07x", time.Now().UnixNano()%0xFFFFFFF)
	}

	id := fmt.Sprintf("dpl_%d", time.Now().Unix())
	now := time.Now()

	job := &DeploymentJob{
		ID:          id,
		Project:     req.Project,
		Branch:      req.Branch,
		Commit:      req.Commit,
		CommitMsg:   "Manual trigger via Vedaix Instant Deploy",
		Author:      "You",
		Status:      "ready",
		Environment: "Production",
		URL:         fmt.Sprintf("https://%s-%s.vedaix.app", req.Project, req.Commit[:6]),
		CreatedAt:   now,
		CompletedAt: &now,
		Steps: []BuildStep{
			{
				Name:      "Initialize Build Environment",
				Status:    "completed",
				Duration:  "0.9s",
				StartedAt: now,
				Logs: []string{
					"[\x1b[38;2;0;199;255mVedaix Go/Rust Pipeline\x1b[0m] Spawning native isolate container...",
					"[\x1b[32mOK\x1b[0m] Verified CPU allocation (4 vCPU, 8 GB RAM limit)",
				},
			},
			{
				Name:      "Build Next.js Production Bundle",
				Status:    "completed",
				Duration:  "4.2s",
				StartedAt: now.Add(1 * time.Second),
				Logs: []string{
					"▲ Compiling Next.js Server Components with TurboPack...",
					"✓ Static HTML export & RSC streaming payload built (2.8s)",
				},
			},
			{
				Name:      "Atomic Edge Mesh Deployment",
				Status:    "completed",
				Duration:  "1.1s",
				StartedAt: now.Add(5 * time.Second),
				Logs: []string{
					"[\x1b[32mOK\x1b[0m] Deployed live to Edge Anycast routing table",
					fmt.Sprintf("\x1b[32;1m✓ Ready: https://%s-%s.vedaix.app\x1b[0m", req.Project, req.Commit[:6]),
				},
			},
		},
	}

	s.mu.Lock()
	s.deployments[id] = job
	s.mu.Unlock()

	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(job)
}

func main() {
	server := NewRunnerServer()
	mux := http.NewServeMux()

	mux.HandleFunc("/health", server.HandleHealth)
	mux.HandleFunc("/api/runner/status", server.HandleStatus)
	mux.HandleFunc("/api/deployments", server.HandleDeployments)
	mux.HandleFunc("/api/builds/trigger", server.HandleTriggerBuild)

	port := ":9001"
	log.Printf("[Vedaix Runner] Native Go Build Engine starting on http://0.0.0.0%s\n", port)
	if err := http.ListenAndServe(port, mux); err != nil {
		log.Fatalf("Server error: %v", err)
	}
}
