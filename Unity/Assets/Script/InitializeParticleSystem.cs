using System.Collections;
using UnityEngine;
using UnityEngine.UI;
using System.Collections.Generic;

using UnityEngine;

public class InitializeParticleSystem : MonoBehaviour
{
    private void Start()
    {
        // 自身のGameObjectにアタッチされているParticleSystemを初期化
        ParticleSystem particle = GetComponent<ParticleSystem>();
        if (particle != null)
        {
            InitializeParticle(particle);
        }
    }

    private void InitializeParticle(ParticleSystem particle)
    {
        var main = particle.main;
        main.startLifetime = 1.5f;
        main.startSize = 1.5f;      
        main.startColor = Color.white;
        main.simulationSpace = ParticleSystemSimulationSpace.World;

        var emission = particle.emission;
        emission.rateOverTime = 35;

        var shape = particle.shape;
        shape.shapeType = ParticleSystemShapeType.Circle;
        shape.radius = 0.5f;
    }
}